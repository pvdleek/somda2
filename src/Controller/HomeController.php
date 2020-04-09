<?php

namespace App\Controller;

use App\Entity\Block;
use App\Entity\RailNews;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends BaseController
{
    /**
     * @return Response
     */
    public function indexAction(): Response
    {
        $railNews = $this->doctrine
            ->getRepository(RailNews::class)
            ->findBy(['active' => true, 'approved' => true], ['dateTime' => 'DESC'], 5);

        return $this->render('home.html.twig', [
            'columnLeft' => $this->getHomeColumnLeft(),
            'railNews' => $railNews,
        ]);
    }

    /**
     * @return string
     */
    private function getHomeColumnLeft(): string
    {
        $return = '';

        $return .= '<div class="moduleS1"><div>
            <h3>Direct naar:</h3>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td><a href="/forum_home/" class="mainlevel-sidenav">Forum</a></td></tr>
                <tr><td><a href="/spots/" class="mainlevel-sidenav">Recente spots</a></td></tr>
                <tr><td><a href="/drgls/" class="mainlevel-sidenav">Bijzondere ritten</a></td></tr>
                <tr><td><a href="/dienstregeling/" class="mainlevel-sidenav">Dienstregeling v/e trein</a></td></tr>
                <tr><td><a href="/doorkomststaat/" class="mainlevel-sidenav">Doorkomststaat</a></td></tr>
                <tr><td><a href="/invoer/" class="mainlevel-sidenav">Spots invoeren</a></td></tr>
                <tr><td><a href="/matsms/" class="mainlevel-sidenav">Materieelsamenstellingen</a></td></tr>
            </table>
        </div></div><div class="moduleS1"><div>
            <h3>Hulp nodig?</h3>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr><td><a href="/help/" class="mainlevel-sidenav">Wat is Somda</a></td></tr>
                <tr><td><a href="/help/100000/" class="mainlevel-sidenav">Algemene hulp</a></td></tr>
                <tr><td><a href="/nieuws/25/" class="mainlevel-sidenav">Spot invoer richtlijnen</a></td></tr>
            </table>
        </div></div>';

        $return .= '<div class="moduleS3"><div>';
        if ($this->userIsLoggedIn()) {
            $return .= '<h3><strong>Welkom terug</strong></h3>' . $this->getUser()->getName() . ' 
                <br /><br /><a href="/mijnspots/">Mijn spots</a>
                <br /><a href="/instellingen/">Mijn instellingen</a>
                <br /><a href="/instellingen/profiel/">Mijn profiel</a>
                <br /><a href="/mijnsomda_home/">Mijn Somda</a>
                <br /><br /><a href="/forum/ongelezen/">Ongelezen forumberichten</a>';
//			$query = 'SELECT COUNT(*)
//					FROM '.DB_PREFIX.'_forum_favorites
//					WHERE uid = '.$session->uid;
//			$dbset_fav = $db->query($query);
//			list($fav_count) = $db->fetchRow($dbset_fav);
//			if ($fav_count>0) {
//				echo '<br /><a href="'.SITE_URL.'/favorieten/">Favoriete discussies ('.$fav_count.')</a>';
//			}
            $return .= '<br /><br /><a href="/index.php?nav=_nav99&amp;op=logout">Uitloggen</a>';
        } else {
            $return .= '<h3><strong>Direct inloggen</strong></h3>
                <?php echo $form->geefAlleMeldingen(); ?>
                <fieldset class="input">
                    <p id="form-login-username">
                        <label for="login_username">Gebruikersnaam</label>
                        <br /><input id="login_username" type="text" name="login_username" class="inputbox" alt="Gebruikersnaam" maxlength="10" size="18" />
                    </p><p id="form-login-password">
                        <label for="login_password">Wachtwoord</label>
                        <br /><input id="login_password" type="password" name="login_password" class="inputbox" maxlength="15" size="18" alt="Wachtwoord" />
                    </p><p id="form-login-remember">
                        <label for="login_remember">Blijf ingelogd</label>
                        <input id="login_remember" type="checkbox" name="login_remember" class="inputbox" alt="Blijf ingelogd" /> <img alt="" height="16" src="vraagteken.gif" width="16" <?php echo giveMouseOver("Hiermee blijf je op deze computer onbeperkt ingelogd totdat je zelf uitlogt .<br />Zonder dit aan te vinken wordt je na 15 minuten inactiviteit automatisch uitgelogd"); ?> />
                    </p>
                    <input type="submit" name="Submit" class="button" value="Inloggen" />
                </fieldset>
                <ul>
                    <li><a class="regusr" href="/registreren/">Nieuw account maken?</a></li>
                    <li>&nbsp;</li>
                    <li><a class="forgotpass" href="/nieuw_wachtwoord/">Wachtwoord vergeten?</a></li>
                </ul>';
			if (strpos($_SERVER['REQUEST_URI'], 'inloggen') || strpos($_SERVER['REQUEST_URI'], 'login')) {
                echo '<input type="hidden" name="return_url" value="', SITE_URL, '" />';
            } else {
                echo '<input type="hidden" name="return_url" value="', $_SERVER['REQUEST_URI'], '" />';
            }
			echo '<input type="hidden" name="nav" value="_nav99" /><input type="hidden" name="op" value="login" />';
		}
        return $return . '</div></div>';
    }
}
