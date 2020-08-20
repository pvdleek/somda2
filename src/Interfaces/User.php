<?php

namespace App\Interfaces;

use App\Entity\ForumDiscussion;
use App\Entity\ForumFavorite;
use App\Entity\ForumForum;
use App\Entity\Group;
use App\Entity\Spot;
use App\Entity\UserPreferenceValue;
use Symfony\Component\Security\Core\User\UserInterface;

interface User extends UserInterface
{
    public function hasRole(string $role): bool;

    public function addRole(string $role): User;

    public function addGroup(Group $group): User;

    public function getGroups(): array;

    public function addForumFavorite(ForumFavorite $forumFavorite): User;

    public function getForumFavorites(): array;

    public function isForumFavorite(ForumDiscussion $discussion): bool;

    public function addModeratedForum(ForumForum $forumForum): User;

    public function getModeratedForums(): array;

    public function addSpot(Spot $spot): User;

    public function getSpots(): array;

    public function addPreference(UserPreferenceValue $userPreferenceValue): User;

    public function getPreferences(): array;

    public function getSignature(): string;
}
