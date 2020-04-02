<?php

namespace App\Controller;

use App\Entity\ForumPost;
use App\Form\ForumPost as ForumPostForm;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ForumPostController extends ForumBaseController
{
    public function replyAction(int $id)
    {
        /**
         * @var ForumPost $post
         */
        $post = $this->doctrine->getRepository(ForumPost::class)->find($id);
        if (!$this->mayPost($post->getDiscussion()->getForum()) || $post->getDiscussion()->isLocked()) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->formFactory->create(ForumPostForm::class);


        if (false) {
            $post = new SomdaForumPosts();
            $post->Discussion = $discussion;
            $post->authorid = (isset($do_moderator) && $do_moderator == 'on' ? geefConfig('moderator_uid') : $session->uid);
            $post->date = date('Y-m-d');
            $post->time = date('H:i:s');
            $post->sign_on = $do_sign;
            $post->Text->text = trim($post_message);
            $post->save();
            $postid = $post->identifier()['id'];

            $query = "insert ignore into " . DB_PREFIX . "_forum_read_" . substr($session->uid, -1) . " (uid, postid) values (" . $session->uid . ", " . $postid . ")";
            $db->queryF($query);
            $query = "insert into " . DB_PREFIX . "_forum_log (postid, actie) values (" . $postid . ", '0')";
            $db->queryF($query);

            // Check of we wat gebruikers moeten mailen over deze nieuwe reactie
            include_once(geefConfig('site_location') . '/class/mailer.php');
            $domail = new mailer();
            $domail->template = 'forum_new_reply.tpl';
            $domail->fromEmail = geefConfig('email_webmaster');
            $domail->fromName = 'Somda';
            $domail->subject = 'Somda - Nieuwe forumreactie op "' . $discussion->title . '"';
            $domail->assign('DISC_TITLE', $discussion->title);
            $domail->assign('DISC_URL', SITE_URL . '/forum/' . $discussion->id . '/' . $discussion->title . '/');

            $query = 'select p.uid
    from ' . DB_PREFIX . '_forum_favorites f
    join ' . DB_PREFIX . '_users_prefs p on p.uid=f.uid
    where p.uid!=' . $session->uid . ' and f.discussionid=' . $discussion->id . '
    and ((p.prefid=12 and p.value=\'1\') or f.alerting=\'1\')
    group by p.uid';
            $dbset_prefs = $db->query($query);
            $uid_array = array();
            while (list($mail_uid) = $db->fetchRow($dbset_prefs)) {
                $domail->setToUsers($mail_uid);
                $uid_array[] = $mail_uid;
            }
            $domail->send();

            // Zet voor al deze gebruikers de alerting voor dit topic op 2 zodat ze geen mails meer krijgen tot ze het topic bezoeken
            $query = 'update ' . DB_PREFIX . '_forum_favorites
    set alerting=\'2\'
    where discussionid=' . $discussion->id . ' and uid in (' . implode(',', $uid_array) . ')';
            $db->queryF($query);
        }

        return $this->render('forum/reply.html.twig', ['form' => $form->createView()]);
    }
}
