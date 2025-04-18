{{--
    Copyright (c) ppy Pty Ltd <contact@ppy.sh>. Licensed under the GNU Affero General Public License v3.0.
    See the LICENCE file in the repository root for full licence text.
--}}
@php
    $currentUser ??= Auth::user();
    $customization = App\Models\UserProfileCustomization::forUser($currentUser);
@endphp
<div class="account-edit">
    <div class="account-edit__section">
        <h2 class="account-edit__section-title">
            {{ osu_trans('accounts.options.title') }}
        </h2>
    </div>

    <div class="account-edit__input-groups">
        <div class="account-edit__input-group">
            <div class="account-edit-entry account-edit-entry--no-label">
                <div>
                    <div class="account-edit-entry__checkboxes-label">
                        {{ osu_trans('accounts.options.beatmapset_download._') }}
                    </div>
                    <form
                        class="account-edit-entry__checkboxes account-edit-entry__checkboxes--vertical js-account-edit js-account-edit-auto-submit"
                        data-account-edit-type="radio"
                        data-url="{{ route('account.options') }}"
                        data-field="user_profile_customization[beatmapset_download]"
                    >
                        @php
                            $statusIsRendered = false;
                        @endphp
                        @foreach (App\Models\UserProfileCustomization::BEATMAPSET_DOWNLOAD as $name)
                            @if ($name === 'direct' && !$currentUser->isSupporter())
                                @continue
                            @endif
                            <label class="account-edit-entry__checkbox">
                                @include('objects._switch', ['locals' => [
                                    'checked' => $customization['beatmapset_download'] === $name,
                                    'name' => 'user_profile_customization[beatmapset_download]',
                                    'type' => 'radio',
                                    'value' => $name,
                                ]])

                                <span class="account-edit-entry__checkbox-label">
                                    {{ osu_trans("accounts.options.beatmapset_download.{$name}") }}
                                </span>

                                @if (!$statusIsRendered)
                                    <div class="account-edit-entry__checkbox-status">
                                        @include('accounts._edit_entry_status', ['modifiers' => ['left']])
                                    </div>
                                    @php
                                        $statusIsRendered = true;
                                    @endphp
                                @endif
                            </label>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>

        <div class="account-edit__input-group">
            <div
                class="account-edit-entry account-edit-entry--no-label js-account-edit js-account-edit-auto-submit"
                data-url="{{ route('account.options') }}"
                data-user-preferences-update="1"
            >
                <label class="account-edit-entry__checkbox">
                    @include('objects._switch', ['locals' => [
                        'additionalClass'=> 'js-account-edit__input',
                        'checked' => $customization['beatmapset_title_show_original'],
                        'name' => 'user_profile_customization[beatmapset_title_show_original]',
                    ]])

                    <span class="account-edit-entry__checkbox-label">
                        {{ osu_trans('accounts.options.beatmapset_title_show_original') }}
                    </span>

                    <div class="account-edit-entry__checkbox-status">
                        @include('accounts._edit_entry_status', ['modifiers' => ['left']])
                    </div>
                </label>
            </div>
        </div>

        <div class="account-edit__input-group">
            <div
                class="account-edit-entry account-edit-entry--no-label js-account-edit js-account-edit-auto-submit"
                data-url="{{ route('account.options') }}"
                data-skip-ajax-error-popup="1"
                data-user-preferences-update="1"
            >
                <label class="account-edit-entry__checkbox">
                    @include('objects._switch', ['locals' => [
                        'additionalClass'=> 'js-account-edit__input',
                        'checked' => $customization['beatmapset_show_nsfw'],
                        'name' => 'user_profile_customization[beatmapset_show_nsfw]',
                    ]])

                    <span class="account-edit-entry__checkbox-label">
                        {{ osu_trans('accounts.options.beatmapset_show_nsfw') }}
                    </span>

                    <div class="account-edit-entry__checkbox-status">
                        @include('accounts._edit_entry_status', ['modifiers' => ['left']])
                    </div>
                </label>
            </div>
        </div>
    </div>
</div>
