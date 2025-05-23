<?php

// Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
// See the LICENCE file in the repository root for full licence text.

namespace App\Models;

use App\Exceptions\ChangeUsernameException;
use App\Exceptions\InvariantException;
use App\Exceptions\ModelNotSavedException;
use App\Jobs\EsDocument;
use App\Libraries\BBCodeForDB;
use App\Libraries\ChangeUsername;
use App\Libraries\Elasticsearch\Indexable;
use App\Libraries\Session\Store as SessionStore;
use App\Libraries\Transactions\AfterCommit;
use App\Libraries\Uploader;
use App\Libraries\User\AvatarHelper;
use App\Libraries\User\Cover;
use App\Libraries\User\DatadogLoginAttempt;
use App\Libraries\User\ProfileBeatmapset;
use App\Libraries\User\UsernamesForDbLookup;
use App\Libraries\UsernameValidation;
use App\Models\Forum\TopicWatch;
use App\Models\OAuth\Client;
use App\Traits\Memoizes;
use App\Traits\Validatable;
use Cache;
use Carbon\Carbon;
use DB;
use Ds\Set;
use Hash;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\QueryException;
use Laravel\Passport\HasApiTokens;
use League\OAuth2\Server\Exception\OAuthServerException;
use Request;

/**
 * @property-read Collection<UserAccountHistory> $accountHistories
 * @property-read Collection<ApiKey> $apiKeys
 * @property-read Collection<UserBadge> $badges
 * @property-read Collection<BeatmapDiscussionVote> $beatmapDiscussionVotes
 * @property-read Collection<BeatmapDiscussion> $beatmapDiscussions
 * @property-read Collection<BeatmapPlaycount> $beatmapPlaycounts
 * @property-read Collection<Beatmap> $beatmaps
 * @property-read Collection<BeatmapsetNomination> $beatmapsetNominations
 * @property-read Collection<BeatmapsetUserRating> $beatmapsetRatings
 * @property-read Collection<BeatmapsetWatch> $beatmapsetWatches
 * @property-read Collection<Beatmapset> $beatmapsets
 * @property-read Collection<static> $blocks
 * @property-read Collection<Changelog> $changelogs
 * @property-read Collection<Chat\Channel> $channels
 * @property-read Collection<UserClient> $clients
 * @property-read Collection<Comment> $comments
 * @property-read Country|null $country
 * @property string|null $country_acronym
 * @property-write string|null $current_password
 * @property-read Carbon|null $displayed_last_visit
 * @property-read string|null $email
 * @property-read Collection<Event> $events
 * @property-read Collection<FavouriteBeatmapset> $favourites
 * @property-read Collection<Follow> $follows
 * @property-read Collection<Forum\Post> $forumPosts
 * @property-read Collection<static> $friends
 * @property-read GithubUser|null $githubUser
 * @property-read Collection<KudosuHistory> $givenKudosu
 * @property int $group_id
 * @property bool $hide_presence
 * @property bool $lock_email_changes
 * @property-read Collection<UserMonthlyPlaycount> $monthlyPlaycounts
 * @property-read Collection<UserNotificationOption> $notificationOptions
 * @property-read Collection<Client> $oauthClients
 * @property-read Collection<Store\Order> $orders
 * @property int $osu_featurevotes
 * @property int $osu_kudosavailable
 * @property int $osu_kudosdenied
 * @property int $osu_kudostotal
 * @property float $osu_mapperrank
 * @property string|null $osu_playmode
 * @property string[]|null $osu_playstyle
 * @property bool $osu_subscriber
 * @property Carbon|null $osu_subscriptionexpiry
 * @property int $osu_testversion
 * @property-write string|null $password
 * @property-write string|null $password_confirmation
 * @property-read string|null $playmode
 * @property bool $pm_friends_only
 * @property-read Collection<ProfileBanner> $profileBanners
 * @property-read Collection<Beatmapset> $profileBeatmapsetsGraveyard
 * @property-read Collection<Beatmapset> $profileBeatmapsetsLoved
 * @property-read Collection<Beatmapset> $profileBeatmapsetsPending
 * @property-read Collection<Beatmapset> $profileBeatmapsetsRanked
 * @property-read Rank $rank
 * @property-read Collection<RankHighest> $rankHighests
 * @property-read Collection<RankHistory> $rankHistories
 * @property-read Collection<KudosuHistory> $receivedKudosu
 * @property-read Collection<UserRelation> $relations
 * @property string|null $remember_token
 * @property-read Collection<UserReplaysWatchedCount> $replaysWatchedCounts
 * @property-read Collection<UserReport> $reportsMade
 * @property-read Collection<ScorePin> $scorePins
 * @property-read Collection<Score\Best\Fruits> $scoresBestFruits
 * @property-read Collection<Score\Best\Mania> $scoresBestMania
 * @property-read Collection<Score\Best\Osu> $scoresBestOsu
 * @property-read Collection<Score\Best\Taiko> $scoresBestTaiko
 * @property-read Collection<Score\Best\Fruits> $scoresFirstFruits
 * @property-read Collection<Score\Best\Mania> $scoresFirstMania
 * @property-read Collection<Score\Best\Osu> $scoresFirstOsu
 * @property-read Collection<Score\Best\Taiko> $scoresFirstTaiko
 * @property-read Collection<Score\Fruits> $scoresFruits
 * @property-read Collection<Score\Mania> $scoresMania
 * @property-read Collection<Score\Osu> $scoresOsu
 * @property-read Collection<Score\Taiko> $scoresTaiko
 * @property-read Collection<UserSeasonScoreAggregate> $seasonScores
 * @property-read UserStatistics\Fruits|null $statisticsFruits
 * @property-read UserStatistics\Mania|null $statisticsMania
 * @property-read UserStatistics\Mania4k|null $statisticsMania4k
 * @property-read UserStatistics\Mania7k|null $statisticsMania7k
 * @property-read UserStatistics\Osu|null $statisticsOsu
 * @property-read UserStatistics\Taiko|null $statisticsTaiko
 * @property-read Collection<Store\Address> $storeAddresses
 * @property-read Collection<UserDonation> $supporterTagPurchases
 * @property-read Collection<UserDonation> $supporterTags
 * @property-read Team|null $team
 * @property-read TeamApplication|null $teamApplication
 * @property-read Collection<OAuth\Token> $tokens
 * @property-read Collection<Forum\TopicWatch> $topicWatches
 * @property-read Collection<UserAchievement> $userAchievements
 * @property-read Collection<UserGroup> $userGroups
 * @property-read Collection<UserNotification> $userNotifications
 * @property-read Forum\Post|null $userPage
 * @property-read UserProfileCustomization|null $userProfileCustomization
 * @property string $user_actkey
 * @property int $user_allow_massemail
 * @property bool $user_allow_pm
 * @property int $user_allow_viewemail
 * @property bool $user_allow_viewonline
 * @property string $user_avatar
 * @property int $user_avatar_height
 * @property int $user_avatar_type
 * @property int $user_avatar_width
 * @property string $user_birthday
 * @property string|null $user_colour
 * @property-read Collection<UserCountryHistory> $userCountryHistory
 * @property string $user_dateformat
 * @property string|null $user_discord
 * @property int $user_dst
 * @property string|null $user_email
 * @property-write string|null $user_email_confirmation
 * @property int $user_emailtime
 * @property string|null $user_from
 * @property int $user_full_folder
 * @property int $user_id
 * @property int $user_inactive_reason
 * @property int $user_inactive_time
 * @property string|null $user_interests
 * @property string $user_ip
 * @property string|null $user_jabber
 * @property string $user_lang
 * @property string $user_last_confirm_key
 * @property int $user_last_privmsg
 * @property int $user_last_search
 * @property int $user_last_warning
 * @property Carbon|null $user_lastmark
 * @property string $user_lastpage
 * @property Carbon|null $user_lastpost_time
 * @property Carbon|null $user_lastvisit
 * @property int $user_login_attempts
 * @property int $user_message_rules
 * @property string $user_msnm
 * @property int $user_new_privmsg
 * @property string $user_newpasswd
 * @property bool $user_notify
 * @property int $user_notify_pm
 * @property int $user_notify_type
 * @property string|null $user_occ
 * @property int $user_options
 * @property int $user_passchg
 * @property string $user_password
 * @property int|null $user_perm_from
 * @property string $user_permissions
 * @property int $user_post_show_days
 * @property string $user_post_sortby_dir
 * @property string $user_post_sortby_type
 * @property int $user_posts
 * @property int|null $user_rank
 * @property Carbon $user_regdate
 * @property string $user_sig
 * @property string $user_sig_bbcode_bitfield
 * @property string $user_sig_bbcode_uid
 * @property int $user_style
 * @property float $user_timezone
 * @property int $user_topic_show_days
 * @property string $user_topic_sortby_dir
 * @property string $user_topic_sortby_type
 * @property string|null $user_twitter
 * @property int $user_type
 * @property int $user_unread_privmsg
 * @property int $user_warnings
 * @property string|null $user_website
 * @property string $username
 * @property-read Collection<UsernameChangeHistory> $usernameChangeHistory
 * @property-read Collection<UsernameChangeHistory> $usernameChangeHistoryPublic publically visible UsernameChangeHistory containing only user_id and username_last
 * @property string $username_clean
 * @property string|null $username_previous
 * @property int|null $userpage_post_id
 * @method static Builder default()
 * @method static Builder eagerloadForListing()
 * @method static Builder online()
 */
class User extends Model implements AfterCommit, AuthenticatableContract, HasLocalePreference, Indexable, Traits\ReportableInterface
{
    use Authenticatable, HasApiTokens, Memoizes, Traits\Es\UserSearch, Traits\Reportable, Traits\UserScoreable, Traits\UserStore, Validatable;

    const PLAYSTYLES = [
        'mouse' => 1,
        'keyboard' => 2,
        'tablet' => 4,
        'touch' => 8,
    ];

    const CACHING = [
        'follower_count' => [
            'key' => 'followerCount',
            'duration' => 43200, // 12 hours
        ],
        'mapping_follower_count' => [
            'key' => 'moddingFollowerCount',
            'duration' => 43200, // 12 hours
        ],
    ];

    const INACTIVE_DAYS = 180;

    const MAX_FIELD_LENGTHS = [
        'user_discord' => 37, // max 32char username + # + 4-digit discriminator
        'user_from' => 25,
        'user_interests' => 30,
        'user_occ' => 30,
        'user_sig' => 3000,
        'user_twitter' => 255,
        'user_website' => 200,
    ];

    public $password = null;
    public $timestamps = false;

    protected $attributes = [
        'user_allow_pm' => true,
    ];
    protected $casts = [
        'lock_email_changes' => 'boolean',
        'osu_subscriber' => 'boolean',
        'user_allow_pm' => 'boolean',
        'user_allow_viewonline' => 'boolean',
        'user_lastmark' => 'datetime',
        'user_lastpost_time' => 'datetime',
        'user_lastvisit' => 'datetime',
        'user_notify' => 'boolean',
        'user_regdate' => 'datetime',
        'user_timezone' => 'float',
    ];
    protected $dateFormat = 'U';
    protected $primaryKey = 'user_id';
    protected $table = 'phpbb_users';

    private Cover $cover;
    private Uploader $customCover;
    private $validateCurrentPassword = false;
    private $validatePasswordConfirmation = false;
    private $passwordConfirmation = null;
    private $currentPassword = null;

    private $emailConfirmation = null;
    private $validateEmailConfirmation = false;

    private $isSessionVerified;

    public static function statisticsRelationName(string $ruleset, ?string $variant = null): ?string
    {
        if (!Beatmap::isModeValid($ruleset) || !Beatmap::isVariantValid($ruleset, $variant)) {
            return null;
        }

        $variantSuffix = $variant === null ? '' : "_{$variant}";

        return 'statistics'.studly_case("{$ruleset}{$variantSuffix}");
    }

    public function userCountryHistory(): HasMany
    {
        return $this->hasMany(UserCountryHistory::class);
    }

    public function team(): HasOneThrough
    {
        return $this->hasOneThrough(
            Team::class,
            TeamMember::class,
            'user_id',
            'id',
            'user_id',
            'team_id',
        );
    }

    public function teamApplication(): HasOne
    {
        return $this->hasOne(TeamApplication::class);
    }

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    public function usernameChangeCost()
    {
        $minTier = $this->usernameChangeHistory()->paid()->exists() ? 1 : 0;

        $tier = max(
            $this->usernameChangeHistory()
                ->paid()
                ->where('timestamp', '>', Carbon::now()->subYearsNoOverflow(3))
                ->count(),
            $minTier,
        );

        return match ($tier) {
            0 => 0,
            1 => 8,
            2 => 16,
            3 => 32,
            4 => 64,
            default => 100,
        };
    }

    public function revertUsername($type = 'revert'): UsernameChangeHistory
    {
        // TODO: normalize validation with changeUsername.
        if ($this->user_id <= 1) {
            throw new ChangeUsernameException('user_id is not valid');
        }

        if (!presence($this->username_previous)) {
            throw new ChangeUsernameException('username_previous is blank.');
        }

        return $this->updateUsername($this->username_previous, $type);
    }

    public function changeUsername(string $newUsername, string $type): UsernameChangeHistory
    {
        $errors = $this->validateChangeUsername($newUsername, $type);
        if ($errors->isAny()) {
            throw new ChangeUsernameException($errors);
        }

        return $this->getConnection()->transaction(function () use ($newUsername, $type) {
            static::findAndRenameUserForInactive($newUsername);

            return $this->updateUsername($newUsername, $type);
        });
    }

    public function renameIfInactive(): ?UsernameChangeHistory
    {
        if ($this->getUsernameAvailableAt() <= Carbon::now()) {
            $newUsername = "{$this->username}_old";

            return $this->tryUpdateUsername(0, $newUsername, 'inactive');
        }

        return null;
    }

    private function tryUpdateUsername(int $try, string $newUsername, string $type): UsernameChangeHistory
    {
        $name = $try > 0 ? "{$newUsername}_{$try}" : $newUsername;

        try {
            return $this->updateUsername($name, $type);
        } catch (QueryException $ex) {
            // Maybe use different suffix altogether if people manage
            // to try using same name more than 21 times.
            if (!is_sql_unique_exception($ex) || $try > 20) {
                throw $ex;
            }

            return $this->tryUpdateUsername($try + 1, $newUsername, $type);
        }
    }

    private function updateUsername(string $newUsername, string $type): UsernameChangeHistory
    {
        $oldUsername = $type === 'revert' ? null : $this->getOriginal('username');
        $this->username_previous = $oldUsername;
        $this->username = $newUsername;

        return DB::transaction(function () use ($newUsername, $oldUsername, $type) {
            Forum\Forum::where('forum_last_poster_id', $this->user_id)->update(['forum_last_poster_name' => $newUsername]);
            // DB::table('phpbb_moderator_cache')->where('user_id', $this->user_id)->update(['username' => $newUsername]);
            Forum\Post::where('poster_id', $this->user_id)->update(['post_username' => $newUsername]);
            Forum\Topic::where('topic_poster', $this->user_id)
                ->update(['topic_first_poster_name' => $newUsername]);
            Forum\Topic::where('topic_last_poster_id', $this->user_id)
                ->update(['topic_last_poster_name' => $newUsername]);

            $history = $this->usernameChangeHistory()->create([
                'username' => $newUsername,
                'username_last' => $oldUsername,
                'timestamp' => Carbon::now(),
                'type' => $type,
            ]);

            if (!$history->exists) {
                throw new ModelNotSavedException('failed saving model');
            }

            $skipValidations = in_array($type, ['inactive', 'revert'], true);
            $this->saveOrExplode(['skipValidations' => $skipValidations]);

            return $history;
        });
    }

    public static function cleanUsername($username)
    {
        return strtolower($username);
    }

    public static function findAndRenameUserForInactive($username): ?self
    {
        $existing = static::findByUsernameForInactive($username);
        if ($existing !== null) {
            $existing->renameIfInactive();
            // TODO: throw if expected rename doesn't happen?
        }

        return $existing;
    }

    // TODO: be able to change which connection this runs on?
    public static function findByUsernameForInactive($username): ?self
    {
        return static::whereIn(
            'username',
            UsernamesForDbLookup::make($username, trimPrefix: false),
        )->first();
    }

    public static function checkWhenUsernameAvailable($username): Carbon
    {
        $user = static::findByUsernameForInactive($username);
        if ($user !== null) {
            return $user->getUsernameAvailableAt();
        }

        $lastUsage = UsernameChangeHistory::where('username_last', $username)
            ->where('type', '<>', 'inactive') // don't include changes caused by inactives; this validation needs to be removed on normal save.
            ->orderBy('change_id', 'desc')
            ->first();

        if ($lastUsage === null) {
            return Carbon::now();
        }

        return Carbon::parse($lastUsage->timestamp)->addDays(static::INACTIVE_DAYS);
    }

    public function getUsernameAvailableAt(): Carbon
    {
        $playCount = $this->playCount();

        $allGroupIds = array_merge([$this->group_id], $this->groupIds()['active']);
        $allowedGroupIds = array_map(function ($groupIdentifier) {
            return app('groups')->byIdentifier($groupIdentifier)->getKey();
        }, $GLOBALS['cfg']['osu']['user']['allowed_rename_groups']);

        // only users which groups are all in the whitelist can be renamed
        if (count(array_diff($allGroupIds, $allowedGroupIds)) > 0) {
            return Carbon::now()->addYears(10);
        }

        if ($this->isRestricted()) {
            $minDays = 0;
            $expMod = 0.35;
            $linMod = 0.75;
        } else {
            $minDays = static::INACTIVE_DAYS;
            $expMod = 1;
            $linMod = 1;
        }

        // This is a exponential decay function with the identity 1-e^{-$playCount}.
        // The constant multiplier of 1580 causes the formula to flatten out at around 1580 days (~4.3 years).
        // $playCount is then divided by the constant value 5900 causing it to flatten out at about 40,000 plays.
        // A linear bonus of $playCount * 8 / 5900 is added to reward long-term players.
        // Furthermore, when the user is restricted, the exponential decay function and the linear bonus are lowered.
        // An interactive graph of the formula can be found at https://www.desmos.com/calculator/s7bxytxbbt

        return $this->user_lastvisit
            ->addDays(
                intval(
                    $minDays +
                    1580 * (1 - pow(M_E, $playCount * $expMod * -1 / 5900)) +
                    ($playCount * $linMod * 8 / 5900)
                )
            );
    }

    public function validateChangeUsername(string $username, string $type = 'paid')
    {
        return (new ChangeUsername($this, $username, $type))->validate();
    }

    public static function lookup($usernameOrId, $type = null, $findAll = false): ?self
    {
        if (!present($usernameOrId)) {
            return null;
        }

        switch ($type) {
            case 'username':
                $searchUsernames = UsernamesForDbLookup::make($usernameOrId);

                $user = static::where(fn ($query) => $query
                    ->whereIn('username', $searchUsernames)
                    ->orWhereIn('username_clean', $searchUsernames));
                break;

            case 'id':
                $user = static::where('user_id', $usernameOrId);
                break;

            default:
                return ctype_digit((string) $usernameOrId)
                    ? static::lookup($usernameOrId, 'id', $findAll)
                    : static::lookup($usernameOrId, 'username', $findAll);
        }

        if (!$findAll) {
            $user->default();
        }

        $user = $user->first();

        if ($user !== null && $user->hasProfile()) {
            return $user;
        }

        return null;
    }

    public static function lookupWithHistory($usernameOrId, $type = null, $findAll = false)
    {
        $user = static::lookup($usernameOrId, $type, $findAll);

        if ($user !== null) {
            return $user;
        }

        // don't perform username change history lookup if we're searching by ID
        // TODO: remove this parameter and always rely on `@` prefix or digit check.
        if ($type === 'id') {
            return null;
        }

        // also reject looking up history when it's all digit and not explicit username search
        if ($type !== 'username' && ctype_digit($usernameOrId)) {
            return null;
        }

        $change = UsernameChangeHistory::visible()
            ->whereIn('username_last', UsernamesForDbLookup::make($usernameOrId))
            ->orderBy('change_id', 'desc')
            ->first();

        if ($change !== null) {
            return static::lookup($change->user_id, 'id');
        }
    }

    public function addToGroup(Group $group, ?array $playmodes = null, ?self $actor = null): void
    {
        $playmodes = array_unique($playmodes ?? []);

        if (!$group->has_playmodes && $playmodes !== []) {
            throw new InvariantException('Group does not allow playmodes');
        }

        $invalidPlaymodes = array_diff($playmodes, array_keys(Beatmap::MODES));

        if ($invalidPlaymodes !== []) {
            throw new InvariantException('Invalid playmodes: '.implode(', ', $invalidPlaymodes));
        }

        $activeUserGroup = $this->findUserGroup($group, true);

        if ($activeUserGroup === null) {
            $userGroup = $this
                ->userGroups()
                ->firstOrNew(['group_id' => $group->getKey()])
                ->setRelation('group', $group)
                ->fill([
                    'playmodes' => $playmodes,
                    'user_pending' => false,
                ]);

            $this->getConnection()->transaction(function () use ($actor, $group, $userGroup) {
                UserGroupEvent::logUserAdd($actor, $this, $group, $userGroup->playmodes);

                $userGroup->save();
            });
        } else {
            $previousPlaymodes = $activeUserGroup->playmodes ?? [];
            $playmodesAdded = array_values(array_diff($playmodes, $previousPlaymodes));
            $playmodesRemoved = array_values(array_diff($previousPlaymodes, $playmodes));

            if ($playmodesAdded === [] && $playmodesRemoved === []) {
                return;
            }

            $this->getConnection()->transaction(function () use ($activeUserGroup, $actor, $group, $playmodes, $playmodesAdded, $playmodesRemoved) {
                if ($playmodesAdded !== []) {
                    UserGroupEvent::logUserAddPlaymodes($actor, $this, $group, $playmodesAdded);
                }

                if ($playmodesRemoved !== []) {
                    UserGroupEvent::logUserRemovePlaymodes($actor, $this, $group, $playmodesRemoved);
                }

                $activeUserGroup->update(['playmodes' => $playmodes]);
            });
        }

        $this->unsetRelation('userGroups');
        $this->resetMemoized();
    }

    public function removeFromGroup(Group $group, ?self $actor = null): void
    {
        $userGroup = $this->findUserGroup($group, false);

        if ($userGroup === null) {
            return;
        }

        $this->getConnection()->transaction(function () use ($actor, $group, $userGroup) {
            if (!$userGroup->user_pending) {
                UserGroupEvent::logUserRemove($actor, $this, $group);
            }

            $userGroup->delete();

            if ($this->group_id === $group->getKey()) {
                $this->setDefaultGroup(app('groups')->byIdentifier('default'));
            }
        });

        $this->unsetRelation('userGroups');
        $this->resetMemoized();
    }

    public function setDefaultGroup(Group $group, ?self $actor = null): void
    {
        $this->getConnection()->transaction(function () use ($actor, $group) {
            if ($this->findUserGroup($group, true) === null) {
                $this->addToGroup($group, null, $actor);
            }

            if ($this->group_id !== $group->getKey()) {
                UserGroupEvent::logUserSetDefault($actor, $this, $group);
            }

            $this->update([
                'group_id' => $group->getKey(),
                'user_colour' => $group->group_colour,
                'user_rank' => $group->group_rank,
            ]);
        });
    }

    public function setUserFromAttribute($value)
    {
        $this->attributes['user_from'] = e(unzalgo($value));
    }

    public function setUserInterestsAttribute($value)
    {
        $this->attributes['user_interests'] = e(unzalgo($value));
    }

    public function setUserOccAttribute($value)
    {
        $this->attributes['user_occ'] = e(unzalgo($value));
    }

    public function setUserSigAttribute($value)
    {
        $bbcode = new BBCodeForDB($value);
        $this->attributes['user_sig'] = $bbcode->generate();
        $this->attributes['user_sig_bbcode_uid'] = $bbcode->uid;
    }

    public function setUserStyleAttribute(?int $value): void
    {
        if ($value === null || $value < 1 || $value > 360) {
            $value = 0;
        }
        $this->attributes['user_style'] = $value;
    }

    public function setUserWebsiteAttribute($value)
    {
        // doubles as casting to empty string for not null constraint
        // allowing zalgo in urls sounds like a terrible idea.
        $value = unzalgo(trim($value), 0);

        // FIXME: this can probably be removed after old site is deactivated
        //        as there's same check in getter function.
        if (present($value) && !is_http($value)) {
            $value = "https://{$value}";
        }

        $this->attributes['user_website'] = $value;
    }

    public function setOsuPlaystyleAttribute($value)
    {
        $styles = 0;

        if ($value !== null) {
            foreach (self::PLAYSTYLES as $type => $bit) {
                if (in_array($type, $value, true)) {
                    $styles += $bit;
                }
            }
        }

        $this->attributes['osu_playstyle'] = $styles;
    }

    public function setPmFriendsOnlyAttribute($value)
    {
        $this->user_allow_pm = !$value;
    }

    public function setHidePresenceAttribute($value)
    {
        $this->user_allow_viewonline = !$value;
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = $value;
        $this->username_clean = static::cleanUsername($value);
    }

    public function isLoginBlocked()
    {
        return $this->user_email === null;
    }

    public function isSpecial()
    {
        return $this->user_id !== null && present($this->user_colour);
    }

    public function setUserTwitterAttribute($value)
    {
        $this->attributes['user_twitter'] = trim(ltrim($value, '@'));
    }

    public function setUserDiscordAttribute($value)
    {
        $this->attributes['user_jabber'] = trim($value);
    }

    public function setUserColourAttribute($value)
    {
        // also functions for casting null to string
        $this->attributes['user_colour'] = ltrim($value, '#');
    }

    public function getAttribute($key)
    {
        return match ($key) {
            'cover_preset_id',
            'custom_cover_filename',
            'group_id',
            'laravel_through_key', // added by hasManyThrough relation in Beatmap
            'osu_featurevotes',
            'osu_kudosavailable',
            'osu_kudosdenied',
            'osu_kudostotal',
            'osu_mapperrank',
            'osu_playmode',
            'osu_testversion',
            'remember_token',
            'user_actkey',
            'user_allow_massemail',
            'user_allow_viewemail',
            'user_avatar_height',
            'user_avatar_type',
            'user_avatar_width',
            'user_birthday',
            'user_dateformat',
            'user_dst',
            'user_email',
            'user_emailtime',
            'user_full_folder',
            'user_id',
            'user_inactive_reason',
            'user_inactive_time',
            'user_ip',
            'user_last_confirm_key',
            'user_last_privmsg',
            'user_last_search',
            'user_last_warning',
            'user_lastpage',
            'user_login_attempts',
            'user_message_rules',
            'user_msnm',
            'user_new_privmsg',
            'user_newpasswd',
            'user_notify_pm',
            'user_notify_type',
            'user_options',
            'user_passchg',
            'user_password',
            'user_perm_from',
            'user_permissions',
            'user_post_show_days',
            'user_post_sortby_dir',
            'user_post_sortby_type',
            'user_posts',
            'user_sig',
            'user_sig_bbcode_bitfield',
            'user_sig_bbcode_uid',
            'user_topic_show_days',
            'user_topic_sortby_dir',
            'user_topic_sortby_type',
            'user_type',
            'user_unread_privmsg',
            'user_warnings',
            'username',
            'username_clean',
            'username_previous',
            'userpage_post_id' => $this->getRawAttribute($key),

            // boolean, default to false for null value
            'lock_email_changes',
            'osu_subscriber',
            'user_allow_pm',
            'user_allow_viewonline',
            'user_notify' => (bool) $this->getRawAttribute($key),

            // float
            'user_timezone' => (float) $this->getRawAttribute($key),

            // datetime but unix timestamp
            'user_lastmark',
            'user_lastpost_time',
            'user_lastvisit',
            'user_regdate' => Carbon::createFromTimestamp($this->getRawAttribute($key)),

            // datetime
            'osu_subscriptionexpiry' => $this->getTimeFast($key),

            // custom cast
            'displayed_last_visit' => $this->getDisplayedLastVisit(),
            'osu_playstyle' => $this->getOsuPlaystyle(),
            'playmode' => $this->getPlaymode(),
            'user_avatar' => AvatarHelper::url($this),
            'user_colour' => $this->getUserColour(),
            'user_rank' => $this->getUserRank(),
            'user_style' => $this->getUserStyle(),
            'user_website' => $this->getUserWebsite(),

            // one-liner cast
            'country_acronym' => presence($this->getRawAttribute($key)),
            'email' => $this->user_email,
            'hide_presence' => !$this->user_allow_viewonline,
            'id' => $this->getKey(), // used by clockwork
            'name' => null, // used by mailer
            'pm_friends_only' => !$this->user_allow_pm,
            'user_discord' => $this->user_jabber,
            'user_from' => presence(html_entity_decode_better($this->getRawAttribute($key))),
            'user_interests' => presence(html_entity_decode_better($this->getRawAttribute($key))),
            'user_jabber' => presence($this->getRawAttribute($key)),
            'user_lang' => get_valid_locale($this->getRawAttribute($key)),
            'user_occ' => presence(html_entity_decode_better($this->getRawAttribute($key))),
            'user_twitter' => presence(ltrim($this->getRawAttribute($key), '@')),

            // relations
            'accountHistories',
            'apiKeys',
            'badges',
            'beatmapDiscussionVotes',
            'beatmapDiscussions',
            'beatmapPlaycounts',
            'beatmaps',
            'beatmapsetNominations',
            'beatmapsetRatings',
            'beatmapsetWatches',
            'beatmapsets',
            'blocks',
            'changelogs',
            'channels',
            'clients',
            'comments',
            'country',
            'dailyChallengeUserStats',
            'events',
            'favourites',
            'follows',
            'forumPosts',
            'friends',
            'githubUser',
            'givenKudosu',
            'legacyIrcKey',
            'monthlyPlaycounts',
            'notificationOptions',
            'oauthClients',
            'orders',
            'pivot', // laravel built-in relation when using belongsToMany
            'profileBanners',
            'profileBannersActive',
            'profileBeatmapsetsGraveyard',
            'profileBeatmapsetsLoved',
            'profileBeatmapsetsPending',
            'profileBeatmapsetsRanked',
            'rank',
            'rankHighests',
            'rankHistories',
            'receivedKudosu',
            'relationFriends',
            'relations',
            'replaysWatchedCounts',
            'reportedIn',
            'reportsMade',
            'scorePins',
            'scoresBestFruits',
            'scoresBestMania',
            'scoresBestOsu',
            'scoresBestTaiko',
            'scoresFirstFruits',
            'scoresFirstMania',
            'scoresFirstOsu',
            'scoresFirstTaiko',
            'scoresFruits',
            'scoresMania',
            'scoresOsu',
            'scoresTaiko',
            'soloScores',
            'statisticsFruits',
            'statisticsMania',
            'statisticsMania4k',
            'statisticsMania7k',
            'statisticsOsu',
            'statisticsTaiko',
            'storeAddresses',
            'supporterTagPurchases',
            'supporterTags',
            'team',
            'teamApplication',
            'tokens',
            'topicWatches',
            'userAchievements',
            'userCountryHistory',
            'userGroups',
            'userNotifications',
            'userPage',
            'userProfileCustomization',
            'usernameChangeHistory',
            'usernameChangeHistoryPublic' => $this->getRelationValue($key),
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Checker Functions
    |--------------------------------------------------------------------------
    |
    | This checks to see if a user is in a specified group.
    | You should try to be specific.
    |
    */

    public function inGroupWithPlaymode($groupIdentifier, $playmode = null)
    {
        $group = app('groups')->byIdentifier($groupIdentifier);
        $isGroup = $this->isGroup($group);

        if ($isGroup === false || $playmode === null) {
            return $isGroup;
        }

        $groupModes = $this->findUserGroup($group, true)->actualRulesets();

        return in_array($playmode, $groupModes ?? [], true);
    }

    public function isNAT(?string $mode = null)
    {
        return $this->inGroupWithPlaymode('nat', $mode);
    }

    public function isAdmin()
    {
        return $this->isGroup(app('groups')->byIdentifier('admin'));
    }

    public function isChatAnnouncer()
    {
        return $this->findUserGroup(app('groups')->byIdentifier('announce'), true) !== null;
    }

    public function isGMT()
    {
        return $this->isGroup(app('groups')->byIdentifier('gmt'));
    }

    public function isBNG(?string $mode = null)
    {
        return $this->isFullBN($mode) || $this->isLimitedBN($mode);
    }

    public function isFullBN(?string $mode = null)
    {
        return $this->inGroupWithPlaymode('bng', $mode);
    }

    public function isLimitedBN(?string $mode = null)
    {
        return $this->inGroupWithPlaymode('bng_limited', $mode);
    }

    public function isDev()
    {
        return $this->isGroup(app('groups')->byIdentifier('dev'));
    }

    public function isModerator()
    {
        return $this->isGMT() || $this->isNAT();
    }

    public function isAlumni()
    {
        return $this->isGroup(app('groups')->byIdentifier('alumni'));
    }

    public function isRegistered()
    {
        return $this->isGroup(app('groups')->byIdentifier('default'));
    }

    public function isProjectLoved()
    {
        return $this->isGroup(app('groups')->byIdentifier('loved'));
    }

    public function isBot()
    {
        return $this->group_id === app('groups')->byIdentifier('bot')->getKey();
    }

    public function hasSupported()
    {
        return $this->getRawAttribute('osu_subscriptionexpiry') !== null;
    }

    public function isSupporter()
    {
        return $this->osu_subscriber === true;
    }

    public function isActive()
    {
        static $monthInSecond = 30 * 86400;

        return time() - $this->getRawAttribute('user_lastvisit') < $monthInSecond;
    }

    /*
     * almost like !isActive but different duration
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return time() - $this->getRawAttribute('user_lastvisit') > $GLOBALS['cfg']['osu']['user']['inactive_seconds_verification'];
    }

    public function isOnline()
    {
        return !$this->hide_presence
            && time() - $this->getRawAttribute('user_lastvisit') < $GLOBALS['cfg']['osu']['user']['online_window'];
    }

    public function isPrivileged()
    {
        return $this->isAdmin()
            || $this->isDev()
            || $this->isGMT()
            || $this->isBNG()
            || $this->isNAT()
            || $this->isProjectLoved();
    }

    public function isBanned()
    {
        return $this->user_type === 1;
    }

    public function isOld()
    {
        return preg_match('/_old(_\d+)?$/', $this->username) === 1;
    }

    public function isRestricted()
    {
        return $this->isBanned() || $this->user_warnings > 0;
    }

    public function isSilenced()
    {
        return $this->memoize(__FUNCTION__, function () {
            if ($this->isRestricted()) {
                return true;
            }

            $lastBan = $this->accountHistories()->bans()->first();

            return $lastBan !== null &&
                $lastBan->period !== 0 &&
                $lastBan->endTime()->isFuture();
        });
    }

    public function trashed()
    {
        return starts_with($this->username, 'DeletedUser_');
    }

    /**
     * User group to be displayed in preference over other groups.
     */
    public function defaultGroup(): Group
    {
        $groups = app('groups');

        if ($this->group_id === $groups->byIdentifier('admin')->getKey()) {
            return $groups->byIdentifier('default');
        }

        return $groups->byId($this->group_id) ?? $groups->byIdentifier('default');
    }

    public function groupIds()
    {
        return $this->memoize(__FUNCTION__, function () {
            $ret = [
                'active' => [],
                'pending' => [],
            ];

            foreach ($this->userGroups as $userGroup) {
                $key = $userGroup->user_pending ? 'pending' : 'active';
                $ret[$key][] = $userGroup->group_id;
            }

            return $ret;
        });
    }


    public function findUserGroup($group, bool $activeOnly): ?UserGroup
    {
        $byGroupId = $this->memoize(__FUNCTION__.':byGroupId', fn () => $this->userGroups->keyBy('group_id'));

        $userGroup = $byGroupId->get($group->getKey());

        if ($userGroup === null || ($activeOnly && $userGroup->user_pending)) {
            return null;
        }

        return $userGroup;
    }

    /**
     * Check if a user is in a specific group.
     *
     * This will always return false when called on user authenticated using OAuth.
     *
     * @param Group $group
     *
     * @return bool
     */
    public function isGroup($group)
    {
        return $this->findUserGroup($group, true) !== null && $this->token() === null;
    }

    public function badges()
    {
        return $this->hasMany(UserBadge::class);
    }

    public function githubUser(): HasOne
    {
        return $this->hasOne(GithubUser::class);
    }

    public function legacyIrcKey(): HasOne
    {
        return $this->hasOne(LegacyIrcKey::class);
    }

    public function monthlyPlaycounts()
    {
        return $this->hasMany(UserMonthlyPlaycount::class);
    }

    public function notificationOptions()
    {
        return $this->hasMany(UserNotificationOption::class);
    }

    public function replaysWatchedCounts()
    {
        return $this->hasMany(UserReplaysWatchedCount::class);
    }

    public function reportsMade()
    {
        return $this->hasMany(UserReport::class, 'reporter_id');
    }

    public function scorePins()
    {
        return $this->hasMany(ScorePin::class);
    }

    public function userGroups()
    {
        return $this->hasMany(UserGroup::class);
    }

    public function beatmapDiscussionVotes()
    {
        return $this->hasMany(BeatmapDiscussionVote::class);
    }

    public function beatmapDiscussions()
    {
        return $this->hasMany(BeatmapDiscussion::class);
    }

    public function beatmapsets()
    {
        return $this->hasMany(Beatmapset::class);
    }

    public function beatmapsetWatches()
    {
        return $this->hasMany(BeatmapsetWatch::class);
    }

    public function beatmaps()
    {
        return $this->hasMany(Beatmap::class);
    }

    public function clients()
    {
        return $this->hasMany(UserClient::class);
    }

    public function dailyChallengeUserStats(): HasOne
    {
        return $this->hasOne(DailyChallengeUserStats::class);
    }

    public function favourites()
    {
        return $this->hasMany(FavouriteBeatmapset::class);
    }

    public function favouriteBeatmapsets()
    {
        $favouritesTable = (new FavouriteBeatmapset())->getTable();
        $beatmapsetsTable = (new Beatmapset())->getTable();

        return Beatmapset::select("{$beatmapsetsTable}.*")
            ->join($favouritesTable, "{$favouritesTable}.beatmapset_id", '=', "{$beatmapsetsTable}.beatmapset_id")
            ->where("{$favouritesTable}.user_id", '=', $this->user_id)
            ->orderby("{$favouritesTable}.dateadded", 'desc');
    }

    public function beatmapsetNominations()
    {
        return $this->hasMany(BeatmapsetNomination::class);
    }

    public function beatmapsetNominationsToday()
    {
        return $this->beatmapsetNominations()->where('created_at', '>', Carbon::now()->subDays())->count();
    }

    public function beatmapPlaycounts()
    {
        return $this->hasMany(BeatmapPlaycount::class);
    }

    public function apiKeys()
    {
        return $this->hasMany(ApiKey::class);
    }

    public function profileBanners()
    {
        return $this->hasMany(ProfileBanner::class);
    }

    public function profileBannersActive(): HasMany
    {
        return $this->profileBanners()->active()->with('tournamentBanner')->orderBy('banner_id');
    }

    public function storeAddresses()
    {
        return $this->hasMany(Store\Address::class);
    }

    public function rank()
    {
        return $this->belongsTo(Rank::class, 'user_rank');
    }

    public function rankHighests(): HasMany
    {
        return $this->hasMany(RankHighest::class);
    }

    public function rankHistories()
    {
        return $this->hasMany(RankHistory::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_acronym');
    }

    public function seasonScores(): HasMany
    {
        return $this->hasMany(UserSeasonScoreAggregate::class);
    }

    public function statisticsOsu()
    {
        return $this->hasOne(UserStatistics\Osu::class);
    }

    public function statisticsFruits()
    {
        return $this->hasOne(UserStatistics\Fruits::class);
    }

    public function statisticsMania()
    {
        return $this->hasOne(UserStatistics\Mania::class);
    }

    public function statisticsMania4k()
    {
        return $this->hasOne(UserStatistics\Mania4k::class);
    }

    public function statisticsMania7k()
    {
        return $this->hasOne(UserStatistics\Mania7k::class);
    }

    public function statisticsTaiko()
    {
        return $this->hasOne(UserStatistics\Taiko::class);
    }

    public function statistics(string $ruleset, bool $returnQuery = false, ?string $variant = null)
    {
        $relationName = static::statisticsRelationName($ruleset, $variant);

        if ($relationName !== null) {
            return $returnQuery ? $this->$relationName() : $this->$relationName;
        }
    }

    public function scoresOsu()
    {
        return $this->hasMany(Score\Osu::class)->default();
    }

    public function scoresFruits()
    {
        return $this->hasMany(Score\Fruits::class)->default();
    }

    public function scoresMania()
    {
        return $this->hasMany(Score\Mania::class)->default();
    }

    public function scoresTaiko()
    {
        return $this->hasMany(Score\Taiko::class)->default();
    }

    public function scores(string $mode, bool $returnQuery = false)
    {
        if (!Beatmap::isModeValid($mode)) {
            return;
        }

        $relation = 'scores'.studly_case($mode);

        return $returnQuery ? $this->$relation() : $this->$relation;
    }

    public function scoresFirstOsu()
    {
        return $this->hasMany(LegacyScoreFirst\Osu::class)->default();
    }

    public function scoresFirstFruits()
    {
        return $this->hasMany(LegacyScoreFirst\Fruits::class)->default();
    }

    public function scoresFirstMania()
    {
        return $this->hasMany(LegacyScoreFirst\Mania::class)->default();
    }

    public function scoresFirstTaiko()
    {
        return $this->hasMany(LegacyScoreFirst\Taiko::class)->default();
    }

    public function scoresFirst(string $mode, bool $returnQuery = false)
    {
        if (!Beatmap::isModeValid($mode)) {
            return;
        }

        $relation = 'scoresFirst'.studly_case($mode);

        return $returnQuery ? $this->$relation() : $this->$relation;
    }

    public function scoresBestOsu()
    {
        return $this->hasMany(Score\Best\Osu::class)->default();
    }

    public function scoresBestFruits()
    {
        return $this->hasMany(Score\Best\Fruits::class)->default();
    }

    public function scoresBestMania()
    {
        return $this->hasMany(Score\Best\Mania::class)->default();
    }

    public function scoresBestTaiko()
    {
        return $this->hasMany(Score\Best\Taiko::class)->default();
    }

    public function scoresBest(string $mode, bool $returnQuery = false)
    {
        if (!Beatmap::isModeValid($mode)) {
            return;
        }

        $relation = 'scoresBest'.studly_case($mode);

        return $returnQuery ? $this->$relation() : $this->$relation;
    }

    public function soloScores(): HasMany
    {
        return $this->hasMany(Solo\Score::class);
    }

    public function topicWatches()
    {
        return $this->hasMany(TopicWatch::class);
    }

    public function userProfileCustomization()
    {
        return $this->hasOne(UserProfileCustomization::class);
    }

    public function accountHistories()
    {
        return $this->hasMany(UserAccountHistory::class);
    }

    public function userPage()
    {
        return $this->belongsTo(Forum\Post::class, 'userpage_post_id');
    }

    public function userAchievements()
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function usernameChangeHistory()
    {
        return $this->hasMany(UsernameChangeHistory::class);
    }

    public function usernameChangeHistoryPublic()
    {
        return $this->usernameChangeHistory()
            ->visible()
            ->select(['user_id', 'username_last'])
            ->withPresent('username_last')
            ->orderBy('timestamp', 'ASC');
    }

    public function relationFriends(): HasMany
    {
        return $this->relations()->friends()->withMutual();
    }

    public function relations()
    {
        return $this->hasMany(UserRelation::class);
    }

    public function blocks()
    {
        return $this
            ->belongsToMany(static::class, 'phpbb_zebra', 'user_id', 'zebra_id')
            ->wherePivot('foe', true)
            ->default();
    }

    public function friends()
    {
        return $this
            ->belongsToMany(static::class, 'phpbb_zebra', 'user_id', 'zebra_id')
            ->wherePivot('friend', true)
            ->default();
    }

    public function channels()
    {
        return $this->hasManyThrough(
            Chat\Channel::class,
            Chat\UserChannel::class,
            'user_id',
            'channel_id',
            'user_id',
            'channel_id'
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    public function maxBlocks()
    {
        return (int)ceil($this->maxFriends() / 5);
    }

    public function maxFriends()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['user']['max_friends_supporter'] : $GLOBALS['cfg']['osu']['user']['max_friends'];
    }

    public function maxMultiplayerDuration()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['user']['max_multiplayer_duration_supporter'] : $GLOBALS['cfg']['osu']['user']['max_multiplayer_duration'];
    }

    public function maxMultiplayerRooms()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['user']['max_multiplayer_rooms_supporter'] : $GLOBALS['cfg']['osu']['user']['max_multiplayer_rooms'];
    }

    public function maxScorePins()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['user']['max_score_pins_supporter'] : $GLOBALS['cfg']['osu']['user']['max_score_pins'];
    }

    public function beatmapsetDownloadAllowance()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['beatmapset']['download_limit_supporter'] : $GLOBALS['cfg']['osu']['beatmapset']['download_limit'];
    }

    public function beatmapsetFavouriteAllowance()
    {
        return $this->isSupporter() ? $GLOBALS['cfg']['osu']['beatmapset']['favourite_limit_supporter'] : $GLOBALS['cfg']['osu']['beatmapset']['favourite_limit'];
    }

    public function uncachedFollowerCount()
    {
        return UserRelation::where('zebra_id', $this->user_id)->where('friend', 1)->count();
    }

    public function uncachedMappingFollowerCount()
    {
        return Follow::whereMorphedTo('notifiable', $this)
            ->where('subtype', 'mapping')
            ->count();
    }

    public function cacheFollowerCount()
    {
        $count = $this->uncachedFollowerCount();

        Cache::put(
            self::CACHING['follower_count']['key'].':'.$this->user_id,
            $count,
            self::CACHING['follower_count']['duration']
        );

        return $count;
    }

    public function cacheMappingFollowerCount()
    {
        $count = $this->uncachedMappingFollowerCount();

        Cache::put(
            self::CACHING['mapping_follower_count']['key'].':'.$this->user_id,
            $count,
            self::CACHING['mapping_follower_count']['duration']
        );

        return $count;
    }

    public function followerCount()
    {
        return get_int(Cache::get(self::CACHING['follower_count']['key'].':'.$this->user_id)) ?? $this->cacheFollowerCount();
    }

    public function mappingFollowerCount()
    {
        return get_int(Cache::get(self::CACHING['mapping_follower_count']['key'].':'.$this->user_id)) ?? $this->cacheMappingFollowerCount();
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function beatmapsetRatings()
    {
        return $this->hasMany(BeatmapsetUserRating::class);
    }

    public function givenKudosu()
    {
        return $this->hasMany(KudosuHistory::class, 'giver_id');
    }

    public function receivedKudosu()
    {
        return $this->hasMany(KudosuHistory::class, 'receiver_id');
    }

    public function supporterTags()
    {
        return $this->hasMany(UserDonation::class, 'target_user_id');
    }

    public function supporterTagPurchases()
    {
        return $this->hasMany(UserDonation::class);
    }

    public function forumPosts()
    {
        return $this->hasMany(Forum\Post::class, 'poster_id');
    }

    public function changelogs()
    {
        return $this->hasMany(Changelog::class);
    }

    public function oauthClients()
    {
        return $this->hasMany(Client::class);
    }

    public function setPlaymodeAttribute($value)
    {
        $this->osu_playmode = Beatmap::modeInt($value);
    }

    public function blockedUserIds()
    {
        return $this->blocks->pluck('user_id');
    }

    public function userGroupsForBadges()
    {
        return $this->memoize(__FUNCTION__, function () {
            if ($this->isBot()) {
                // Not a query because it's both unnecessary and not guaranteed
                // that the usergroup for "bot" will exist
                return collect([
                    UserGroup::make([
                        'group_id' => app('groups')->byIdentifier('bot')->getKey(),
                        'user_id' => $this->getKey(),
                        'user_pending' => false,
                    ]),
                ]);
            }

            return $this->userGroups
                ->filter(function ($userGroup) {
                    return optional($userGroup->group)->hasBadge();
                })
                ->sort(function ($a, $b) {
                    // If the user has a default group, always show it first
                    if ($a->group_id === $this->group_id) {
                        return -1;
                    }
                    if ($b->group_id === $this->group_id) {
                        return 1;
                    }

                    // Otherwise, sort by display order
                    return $a->group->display_order - $b->group->display_order;
                })
                ->values();
        });
    }

    public function nominationModes()
    {
        return $this->memoize(__FUNCTION__, function () {
            if (!$this->isNAT() && !$this->isBNG()) {
                return;
            }

            $modes = [];

            if ($this->isLimitedBN()) {
                $playmodes = $this->findUserGroup(app('groups')->byIdentifier('bng_limited'), true)->actualRulesets();
                foreach ($playmodes as $playmode) {
                    $modes[$playmode] = 'limited';
                }
            }

            if ($this->isFullBN()) {
                $playmodes = $this->findUserGroup(app('groups')->byIdentifier('bng'), true)->actualRulesets();
                foreach ($playmodes as $playmode) {
                    $modes[$playmode] = 'full';
                }
            }

            if ($this->isNAT()) {
                $playmodes = $this->findUserGroup(app('groups')->byIdentifier('nat'), true)->actualRulesets();
                foreach ($playmodes as $playmode) {
                    $modes[$playmode] = 'full';
                }
            }

            return $modes;
        });
    }

    public function hasBlocked(self $user)
    {
        return $this->memoize(__FUNCTION__, function () {
            return new Set($this->blocks->pluck('user_id'));
        })->contains($user->getKey());
    }

    public function hasFriended(self $user)
    {
        return $this->memoize(__FUNCTION__, function () {
            return new Set($this->friends->pluck('user_id'));
        })->contains($user->getKey());
    }

    public function hasFavourited($beatmapset)
    {
        return $this->memoize(__FUNCTION__, function () {
            return new Set($this->favourites->pluck('beatmapset_id'));
        })->contains($beatmapset->getKey());
    }

    public function remainingHype()
    {
        return $this->memoize(__FUNCTION__, function () {
            $hyped = $this
                ->beatmapDiscussions()
                ->withoutTrashed()
                ->ofType('hype')
                ->where('created_at', '>', Carbon::now()->subWeeks())
                ->count();

            return $GLOBALS['cfg']['osu']['beatmapset']['user_weekly_hype'] - $hyped;
        });
    }

    public function newHypeTime()
    {
        return $this->memoize(__FUNCTION__, function () {
            $earliestWeeklyHype = $this
                ->beatmapDiscussions()
                ->withoutTrashed()
                ->ofType('hype')
                ->where('created_at', '>', Carbon::now()->subWeeks())
                ->orderBy('created_at')
                ->first();

            return $earliestWeeklyHype === null ? null : $earliestWeeklyHype->created_at->addWeeks();
        });
    }

    public function authHash(): string
    {
        return hash('sha256', $this->user_email).':'.hash('sha256', $this->user_password);
    }

    public function resetSessions(?string $excludedSessionId = null): void
    {
        $userId = $this->getKey();
        $sessionIds = SessionStore::ids($userId);
        if ($excludedSessionId !== null) {
            $sessionIds = array_filter(
                $sessionIds,
                fn ($sessionId) => $sessionId !== $excludedSessionId,
            );
        }
        SessionStore::batchDelete($userId, $sessionIds);

        $this
            ->tokens()
            ->with('refreshToken')
            ->chunkById(1000, fn ($tokens) => $tokens->each->revokeRecursive());
    }

    public function title(): ?string
    {
        return $this->rank?->rank_title;
    }

    public function titleUrl(): ?string
    {
        return $this->rank?->url;
    }

    public function hasProfile()
    {
        return $this->getKey() !== null
            && $this->group_id !== app('groups')->byIdentifier('no_profile')->getKey();
    }

    public function hasProfileVisible()
    {
        return $this->hasProfile() && !$this->isRestricted();
    }

    public function updatePage($text)
    {
        if ($this->userPage === null) {
            DB::transaction(function () use ($text) {
                $topic = Forum\Topic::createNew(
                    Forum\Forum::find($GLOBALS['cfg']['osu']['user']['user_page_forum_id']),
                    [
                        'title' => "{$this->username}'s user page",
                        'user' => $this,
                        'body' => $text,
                    ]
                );

                $this->update(['userpage_post_id' => $topic->topic_first_post_id]);
            });
        } else {
            $this
                ->userPage
                ->skipBodyPresenceCheck()
                ->update([
                    'post_text' => $text,
                    'post_edit_user' => $this->getKey(),
                ]);

            if ($this->userPage->validationErrors()->isAny()) {
                throw new ModelNotSavedException($this->userPage->validationErrors()->toSentence());
            }
        }

        return $this->fresh();
    }

    public function supportLength()
    {
        return $this->memoize(__FUNCTION__, function () {
            $supportLength = 0;

            foreach ($this->supporterTagPurchases as $support) {
                if ($support->cancel === true) {
                    $supportLength -= $support->length;
                } else {
                    $supportLength += $support->length;
                }
            }

            return $supportLength;
        });
    }

    public function supportLevel()
    {
        if ($this->osu_subscriber === false) {
            return 0;
        }

        $length = $this->supportLength();

        if ($length < 12) {
            return 1;
        }

        if ($length < 5 * 12) {
            return 2;
        }

        return 3;
    }

    /**
     * Recommended star difficulty.
     *
     * @param string $mode one of Beatmap::MODES
     *
     * @return float
     */
    public function recommendedStarDifficulty(string $mode)
    {
        $stats = $this->statistics($mode);

        return UserStatistics\Model::calculateRecommendedStarDifficulty($stats);
    }

    /**
     * Recommended star difficulty for all modes.
     *
     * @return float
     */
    public function recommendedStarDifficultyAll()
    {
        return $this->memoize(__FUNCTION__, function () {
            $unionQuery = null;

            foreach (Beatmap::MODES as $key => $_value) {
                $query = $this->statistics($key, true)->selectRaw("'{$key}' AS game_mode, rank_score");

                if ($unionQuery === null) {
                    $unionQuery = $query;
                } else {
                    $unionQuery->unionAll($query);
                }
            }

            $stats = $unionQuery->get()->keyBy('game_mode');

            foreach (Beatmap::MODES as $key => $_value) {
                $recs[$key] = UserStatistics\Model::calculateRecommendedStarDifficulty($stats[$key] ?? null);
            }

            return $recs;
        });
    }

    public function refreshForumCache($forum = null, $postsChangeCount = 0)
    {
        if ($forum !== null) {
            if (Forum\Authorize::increasesPostsCount($this, $forum) !== true) {
                $postsChangeCount = 0;
            }

            $newPostsCount = db_unsigned_increment('user_posts', $postsChangeCount);
        } else {
            $newPostsCount = $this->forumPosts()->whereIn('forum_id', Forum\Authorize::postsCountedForums($this))->count();
        }

        $lastPost = $this->forumPosts()->select('post_time')->last();

        // FIXME: not null column, hence default 0. Change column to allow null
        $lastPostTime = $lastPost !== null ? $lastPost->post_time : 0;

        return $this->update([
            'user_posts' => $newPostsCount,
            'user_lastpost_time' => $lastPostTime,
        ]);
    }

    public function scopeDefault($query)
    {
        return $query->where([
            'user_warnings' => 0,
            'user_type' => 0,
        ]);
    }

    public function scopeOnline($query)
    {
        return $query
            ->where('user_allow_viewonline', true)
            ->where('user_lastvisit', '>', time() - $GLOBALS['cfg']['osu']['user']['online_window']);
    }

    public function scopeWithoutBots(Builder $query): Builder
    {
        return $query->whereNot('group_id', app('groups')->byIdentifier('bot')->getKey());
    }

    public function scopeWithoutNoProfile(Builder $query): Builder
    {
        return $query->whereNot('group_id', app('groups')->byIdentifier('no_profile')->getKey());
    }

    public function checkPassword($password)
    {
        return Hash::check($password, $this->getAuthPassword());
    }

    public function validatePasswordConfirmation()
    {
        $this->validatePasswordConfirmation = true;

        return $this;
    }

    public function setPasswordConfirmationAttribute($value)
    {
        $this->passwordConfirmation = $value;
    }

    public function setPasswordAttribute($value)
    {
        // actual user_password assignment is after validation
        $this->password = $value;
    }

    public function validateCurrentPassword()
    {
        $this->validateCurrentPassword = true;

        return $this;
    }

    public function setCurrentPasswordAttribute($value)
    {
        $this->currentPassword = $value;
    }

    /**
     * Enables email presence and confirmation field equality check.
     */
    public function validateEmailConfirmation()
    {
        $this->validateEmailConfirmation = true;

        return $this;
    }

    public function setUserEmailConfirmationAttribute($value)
    {
        $this->emailConfirmation = $value;
    }

    public static function attemptLogin($user, $password, $ip = null)
    {
        $ip = $ip ?? Request::getClientIp() ?? '0.0.0.0';

        if (LoginAttempt::isLocked($ip)) {
            DatadogLoginAttempt::log('locked_ip');

            return osu_trans('users.login.locked_ip');
        }

        $authError = null;

        if ($user === null) {
            $authError = 'nonexistent_user';
        } else {
            $isLoginBlocked = $user->isLoginBlocked();

            if ($isLoginBlocked) {
                $authError = 'user_login_blocked';
            } else {
                if (!$user->checkPassword($password)) {
                    $authError = 'invalid_password';
                }
            }
        }


        if ($authError !== null) {
            DatadogLoginAttempt::log($authError);
            LoginAttempt::logAttempt($ip, $user, 'fail', $password);

            return osu_trans('users.login.failed');
        }

        LoginAttempt::logLoggedIn($ip, $user);
    }

    public static function findForLogin($username, $allowEmail = false)
    {
        $username = trim($username ?? '');

        if ($username === null) {
            return null;
        }

        $query = static::where('username', $username);

        if ($GLOBALS['cfg']['osu']['user']['allow_email_login'] || $allowEmail) {
            $query->orWhere('user_email', strtolower($username));
        }

        return $query->first();
    }

    public static function findAndValidateForPassport($username, $password)
    {
        $user = static::findForLogin($username);
        $authError = static::attemptLogin($user, $password);

        if ($authError === null) {
            return $user;
        }

        throw OAuthServerException::invalidGrant($authError);
    }

    public function cover(): Cover
    {
        return $this->cover ??= new Cover($this);
    }

    public function customCover(): Uploader
    {
        return $this->customCover ??= new Uploader(
            'user-profile-covers',
            $this,
            'custom_cover_filename',
            ['image' => [
                'maxDimensions' => Cover::CUSTOM_COVER_MAX_DIMENSIONS,
                'maxFilesize' => Cover::CUSTOM_COVER_MAX_FILESIZE,
            ]],
        );
    }

    public function playCount()
    {
        return $this->memoize(__FUNCTION__, function () {
            $unionQuery = null;

            foreach (Beatmap::MODES as $key => $_value) {
                $query = $this->statistics($key, true)->select('playcount');

                if ($unionQuery === null) {
                    $unionQuery = $query;
                } else {
                    $unionQuery->unionAll($query);
                }
            }

            return $unionQuery->get()->sum('playcount');
        });
    }

    /**
     * User's previous usernames
     *
     * @param bool $includeCurrent true if previous usernames matching the the current one should be included.
     *
     * @return Collection string
     */
    public function previousUsernames(bool $includeCurrent = false)
    {
        $history = $this->usernameChangeHistoryPublic;

        if (!$includeCurrent) {
            $history = $history->where('username_last', '<>', $this->username);
        }

        return $history->pluck('username_last');
    }

    public function profileBeatmapsetsRanked()
    {
        return $this->beatmapsets()
            ->withStates(['ranked', 'approved', 'qualified'])
            ->active()
            ->with('beatmaps');
    }

    public function profileBeatmapsetsFavourite()
    {
        return $this->favouriteBeatmapsets()
            ->active()
            ->with('beatmaps');
    }

    public function profileBeatmapsetsPending()
    {
        return $this->beatmapsets()->withStates(['pending', 'wip'])->active()->with('beatmaps');
    }

    public function profileBeatmapsetsGraveyard()
    {
        return $this->beatmapsets()
            ->graveyard()
            ->active()
            ->with('beatmaps');
    }

    public function profileBeatmapsetsLoved()
    {
        return $this->beatmapsets()
            ->loved()
            ->active()
            ->with('beatmaps');
    }

    public function profileBeatmapsetsGuest()
    {
        return Beatmapset
            ::where('user_id', '<>', $this->getKey())
            ->whereHas(
                'beatmaps',
                fn (Builder $query) => $query->scoreable()->whereHas(
                    'beatmapOwners',
                    fn (Builder $ownerQuery) => $ownerQuery->where('user_id', $this->getKey())
                )
            )
            ->with('beatmaps');
    }

    public function profileBeatmapsetsNominated()
    {
        return Beatmapset::withStates(['approved', 'ranked'])
            ->whereHas('beatmapsetNominations', fn ($q) => $q->current()->where('user_id', $this->getKey()))
            ->with('beatmaps');
    }

    public function profileBeatmapsetCountByGroupedStatus(string $status)
    {
        return $this->memoize(__FUNCTION__, fn () =>
            ProfileBeatmapset::countByGroupedStatus($this))[$status] ?? 0;
    }

    public function isSessionVerified()
    {
        return $this->isSessionVerified;
    }

    public function markSessionVerified()
    {
        $this->isSessionVerified = true;

        return $this;
    }

    public function isValid()
    {
        $this->validationErrors()->reset();

        if ($this->isDirty('username')) {
            $errors = UsernameValidation::validateUsername($this->username);

            if ($errors->isAny()) {
                $this->validationErrors()->merge($errors);
            }
        }

        if ($this->validateCurrentPassword) {
            if (!$this->checkPassword($this->currentPassword)) {
                $this->validationErrors()->add('current_password', '.wrong_current_password');
            }
        }

        if ($this->validatePasswordConfirmation) {
            if ($this->password !== $this->passwordConfirmation) {
                $this->validationErrors()->add('password_confirmation', '.wrong_password_confirmation');
            }
        }

        if (present($this->password)) {
            if (present($this->username)) {
                if (strpos(strtolower($this->password), strtolower($this->username)) !== false) {
                    $this->validationErrors()->add('password', '.contains_username');
                }
            }

            if (strlen($this->password) < 8) {
                $this->validationErrors()->add('password', '.too_short');
            }

            if (WeakPassword::check($this->password)) {
                $this->validationErrors()->add('password', '.weak');
            }

            if ($this->validationErrors()->isEmpty()) {
                $this->user_password = Hash::make($this->password);
            }
        }

        if ($this->validateEmailConfirmation) {
            if ($this->user_email === null) {
                $this->validationErrors()->add('user_email', '.required');
            }

            if ($this->user_email !== $this->emailConfirmation) {
                $this->validationErrors()->add('user_email_confirmation', '.wrong_email_confirmation');
            }
        }

        if ($this->isDirty('user_email') && present($this->user_email)) {
            $this->isValidEmail();
        }

        $countryAcronym = $this->country_acronym;
        if ($countryAcronym === null) {
            $this->country_acronym = Country::UNKNOWN;
        } elseif ($this->isDirty('country_acronym') && $countryAcronym !== Country::UNKNOWN) {
            if (app('countries')->byCode($countryAcronym) === null) {
                $this->validationErrors()->add('country', '.invalid_country');
            }
        }

        // user_discord is an accessor for user_jabber
        if ($this->isDirty('user_jabber') && present($this->user_discord)) {
            // This is a basic check and not 100% compliant to Discord's spec, only validates that input:
            // - is a 2-32 char username (excluding chars @#:) and 4-digit discriminator for old-style usernames; or,
            // - 2-32 char alphanumeric + period username for new-style usernames; consecutive periods are not validated.
            if (!preg_match('/^([^@#:]{2,32}#\d{4}|[\w.]{2,32})$/i', $this->user_discord)) {
                $this->validationErrors()->add('user_discord', '.invalid_discord');
            }
        }

        if ($this->isDirty('user_twitter') && present($this->user_twitter)) {
            // https://help.twitter.com/en/managing-your-account/twitter-username-rules
            if (!preg_match('/^[a-zA-Z0-9_]{1,15}$/', $this->user_twitter)) {
                $this->validationErrors()->add('user_twitter', '.invalid_twitter');
            }
        }

        $this->validateDbFieldLengths();

        if ($this->isDirty('group_id') && app('groups')->byId($this->group_id) === null) {
            $this->validationErrors()->add('group_id', 'invalid');
        }

        return $this->validationErrors()->isEmpty();
    }

    public function isValidEmail()
    {
        if (!is_valid_email_format($this->user_email)) {
            $this->validationErrors()->add('user_email', '.invalid_email');

            // no point validating further if address isn't valid.
            return false;
        }

        $banlist = DB::table('phpbb_banlist')->where('ban_end', '>=', now()->timestamp)->orWhere('ban_end', 0);
        foreach (model_pluck($banlist, 'ban_email') as $check) {
            if (preg_match('#^'.str_replace('\*', '.*?', preg_quote($check, '#')).'$#i', $this->user_email)) {
                $this->validationErrors()->add('user_email', '.email_not_allowed');

                return false;
            }
        }

        if (static::where('user_id', '<>', $this->getKey())->where('user_email', '=', $this->user_email)->exists()) {
            $this->validationErrors()->add('user_email', '.email_already_used');

            return false;
        }

        return true;
    }

    public function preferredLocale()
    {
        return $this->user_lang;
    }

    public function url(?string $ruleset = null)
    {
        return route('users.show', ['mode' => $ruleset, 'user' => $this->getKey()]);
    }

    public function validationErrorsTranslationPrefix(): string
    {
        return 'user';
    }

    public function save(array $options = [])
    {
        if ($options['skipValidations'] ?? false) {
            return parent::save($options);
        }

        return $this->isValid() && parent::save($options);
    }

    public function afterCommit()
    {
        dispatch(new EsDocument($this));
    }

    protected function newReportableExtraParams(): array
    {
        return [
            'reason' => 'Cheating',
            'user_id' => $this->getKey(),
        ];
    }

    private function getDisplayedLastVisit()
    {
        return $this->hide_presence ? null : $this->user_lastvisit;
    }

    private function getOsuPlaystyle()
    {
        $value = $this->getRawAttribute('osu_playstyle');

        $styles = [];
        foreach (self::PLAYSTYLES as $type => $bit) {
            if (($value & $bit) !== 0) {
                $styles[] = $type;
            }
        }

        return empty($styles) ? null : $styles;
    }

    private function getPlaymode()
    {
        return Beatmap::modeStr($this->osu_playmode);
    }

    private function getUserColour()
    {
        $value = presence($this->getRawAttribute('user_colour'));

        return $value === null
            ? null
            : "#{$value}";
    }

    private function getUserRank()
    {
        $value = $this->getRawAttribute('user_rank');

        return $value === 0 ? null : $value;
    }

    private function getUserStyle()
    {
        $value = $this->getRawAttribute('user_style');

        return $value === 0 ? null : $value;
    }

    private function getUserWebsite()
    {
        $value = presence(trim($this->getRawAttribute('user_website')));

        return $value === null
            ? null
            : (is_http($value)
                ? $value
                : "https://{$value}"
            );
    }
}
