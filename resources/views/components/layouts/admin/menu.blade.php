@props(['companies'])

<x-loading.menu />

<div class="container flex items-center py-3 mb-4 border-b-2 xl:hidden">
    <span class="material-icons text-black js-hamburger-menu">menu</span>

    <div class="flex items-center m-auto">
        <img src="{{ asset('public/img/akaunting-logo-green.svg') }}" class="w-8 m-auto" alt="Akaunting" />
        <span class="ltr:ml-2 rtl:mr-2">{{ Str::limit(setting('company.name'), 22) }}</span>
    </div>

    @can('create-banking-transactions')
        <x-dropdown id="dropdown-mobile-actions">
            <x-slot name="trigger">
                <span class="material-icons pointer-events-none">more_horiz</span>
            </x-slot>

            <x-dropdown.link href="{{ route('transactions.create', ['type' => 'income']) }}">
                {{ trans('general.title.new', ['type' => trans_choice('general.incomes', 1)]) }}
            </x-dropdown.link>

            <x-dropdown.link href="{{ route('transactions.create', ['type' => 'expense']) }}" kind="primary">
                {{ trans('general.title.new', ['type' => trans_choice('general.expenses', 1)]) }}
            </x-dropdown.link>
        </x-dropdown>
    @endcan
</div>

@stack('menu_start')

<div
    x-data="{ }"
    x-init="() => {
        const loadEvent = 'onpagehide' in window ? 'pageshow' : 'load';
        window.addEventListener(loadEvent, () => {
            $refs.realMenu.classList.remove('hidden');
        });
    }"
    x-ref="realMenu"
    class="w-70 h-screen flex hidden fixed top-0 js-menu z-20 xl:z-10 transition-all ltr:-left-80 rtl:-right-80 xl:ltr:left-0 xl:rtl:right-0"
>
    <div class="w-14 py-7 px-1 bg-lilac-900 z-10 menu-scroll overflow-y-auto overflow-x-hidden">
        <div
            data-tooltip-target="tooltip-profile"
            data-tooltip-placement="right"
            class="flex flex-col items-center justify-center mb-5 cursor-pointer menu-button"
            data-menu="profile-menu"
        >
            <span id="menu-profile-icon-cancel" name="account_circle" class="material-icons-outlined w-8 h-8 flex items-center justify-center text-purple text-2xl hidden pointer-events-none">
                account_circle
            </span>

            @if (setting('default.use_gravatar', '0') == '1')
                <img src="{{ user()->picture }}" alt="{{ user()->name }}" class="w-8 h-8 m-auto rounded-full text-transparent" alt="{{ user()->name }}" title="{{ user()->name }}">
            @elseif (is_object(user()->picture))
                <img src="{{ Storage::url(user()->picture->id) }}" class="w-8 h-8 m-auto rounded-full text-transparent" alt="{{ user()->name }}" title="{{ user()->name }}">
            @else
                <span id="menu-profile-icon" name="account_circle" class="material-icons-outlined text-purple w-8 h-8 flex items-center justify-center text-center text-2xl pointer-events-none" alt="{{ user()->name }}" title="{{ user()->name }}">
                    account_circle
                </span>
            @endif
        </div>

        <div id="tooltip-profile" class="inline-block absolute z-20 py-1 px-2 text-sm font-medium rounded-lg bg-white text-gray-900 w-auto border border-gray-200 shadow-sm whitespace-nowrap opacity-0 invisible">
            {{ trans('auth.profile') }}
            <div class="absolute w-2 h-2 before:absolute before:w-2 before:h-2 before:bg-white before:border-gray-200 before:transform before:rotate-45 before:border -left-1 before:border-t-0 before:border-r-0 border-gray-200" data-popper-arrow></div>
        </div>

        <div class="group flex flex-col items-center justify-center menu-toggle-buttons">
            @can('read-notifications')
            <x-tooltip id="tooltip-notifications" placement="right" message="{{ trans_choice('general.notifications', 2) }}">
                <button type="button"
                    @class([
                        'flex items-center menu-button justify-center w-8 h-8 mb-2.5 relative cursor-pointer js-menu-toggles outline-none',
                        'animate-vibrate' => $notification_count,
                    ])
                    data-menu="notifications-menu"
                >
                    <span id="menu-notification-icon" name="notifications" class="material-icons-outlined text-purple text-2xl pointer-events-none">notifications</span>

                    @if ($notification_count)
                        <span data-notification-count class="w-2 h-2 absolute top-2 right-2 inline-flex items-center justify-center p-2.5 text-xs text-white font-bold leading-none transform translate-x-1/2 -translate-y-1/2 bg-orange rounded-full">
                            {{ $notification_count }}
                        </span>
                    @endif
                </button>
            </x-tooltip>
            @endcan

            <x-tooltip id="tooltip-search" placement="right" message="{{ trans('general.search') }}">
                <button type="button" class="flex items-center menu-button justify-center w-8 h-8 mb-2.5 relative cursor-pointer outline-none">
                    <span id="menu-search-icon" name="search" class="material-icons-outlined text-purple text-2xl pointer-events-none">search</span>
                </button>
            </x-tooltip>

            <x-tooltip id="tooltip-new" placement="right" message="{{ trans('general.new') }}">
                <button type="button" class="add-item menu-button flex items-center justify-center w-8 h-8 mb-2.5 cursor-pointer js-menu-toggles" data-menu="add-new-menu">
                    <span id="menu-neww-icon" name="add_circle_outline" class="material-icons-outlined text-purple text-2xl pointer-events-none">add_circle_outline</span>
                </button>
            </x-tooltip>

            <x-tooltip id="tooltip-settings" placement="right" message="{{ trans_choice('general.settings', 2) }}">
                <button type="button" class="settings-item menu-button flex items-center justify-center w-8 h-8 mb-2.5 cursor-pointer js-menu-toggles" data-menu="settings-menu">
                    <span id="menu-settings-icon" name="settings" class="material-icons-outlined text-purple text-2xl pointer-events-none">settings</span>
                </button>
            </x-tooltip>

            <x-tooltip id="tooltip-support" placement="right" message="{{ trans('general.help') }}">
                <x-link href="{{ url(trans('header.support_link')) }}" target="_blank" class="flex items-center justify-center w-8 h-8 mb-2.5 cursor-pointer js-menu-toggles" override="class">
                    <span id="menu-support-icon" class="material-icons-outlined text-purple text-2xl pointer-events-none">support</span>
                </x-link>
            </x-tooltip>
        </div>

        <livewire:menu.favorites />
    </div>

    @stack('main_menu_start')

    <nav class="menu-list js-main-menu" id="sidenav-main">
        @stack('main_menu_company_start')

        <div class="relative mb-5 cursor-pointer">
            <button type="button" class="flex items-center" data-dropdown-toggle="dropdown-menu-company">
                <div class="w-8 h-8 flex items-center justify-center">
                    <img src="{{ asset('public/img/akaunting-logo-green.svg') }}" class="w-6 h-6" alt="Akaunting" />
                </div>

                <div class="flex ltr:ml-2 rtl:mr-2">
                    <span class="w-28 ltr:text-left rtl:text-right block text-base truncate">
                        <x-button.hover>
                            {{ Str::limit(setting('company.name'), 22) }}
                        </x-button.hover>
                    </span>

                    <div class="absolute top-2 ltr:-right-1 rtl:-left-1">
                        <svg class="h-5 w-5 text-gray-400" x-description="Heroicon name: solid/selector" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                </div>
            </button>

            <div id="dropdown-menu-company" class="absolute right-0 mt-3 pt-2 bg-white rounded-md shadow-xl z-20 hidden" style="left: auto; min-width: 10rem;">
                @foreach($companies as $com)
                    <x-link href="{{ route('companies.switch', $com->id) }}" id="menu-company-{{ $com->id }}" class="h-9 leading-9 flex items-center text-sm px-2" override="class" role="menuitem" tabindex="-1">
                        <div class="w-full h-full flex items-center rounded-md px-2 hover:bg-lilac-100">
                            <span class="material-icons-outlined text-purple text-xl">business</span>
                            <span class="ltr:pl-2 rtl:pr-2 text-purple text-xs truncate">{{ Str::limit($com->name, 18) }}</span>
                        </div>
                    </x-link>
                @endforeach

                <x-link href="{{ route('companies.index') }}" class="h-9 leading-9 flex items-center text-sm px-2 mt-2 border-t rounded-bl rounded-br group hover:bg-purple" override="class">
                    <div class="w-full h-full flex items-center rounded-md px-2">
                        <span class="material-icons-outlined text-purple text-xl group-hover:text-white">settings</span>
                        <span class="ltr:pl-2 rtl:pr-2 text-purple text-xs truncate group-hover:text-white">
                            {{ trans('general.title.manage', ['type' => trans_choice('general.companies', 2)]) }}
                        </span>
                    </div>
                </x-link>
            </div>
        </div>

        @stack('main_menu_admin_start')

        <div class="main-menu transform">
            {!! menu('admin') !!}
        </div>

        @stack('main_menu_admin_end')
    </nav>

    @stack('main_menu_end')

    @stack('profile_menu_start')

    <div class="profile-menu user-menu menu-list fixed h-full ltr:-left-80 rtl:-right-80">
        @stack('profile_menu_avatar_start')

        <div class="flex h-12.5">
            @if (setting('default.use_gravatar', '0') == '1')
                <img src="{{ user()->picture }}" alt="{{ user()->name }}" class="w-8 h-8 rounded-full" alt="{{ user()->name }}" title="{{ user()->name }}">
            @elseif (is_object(user()->picture))
                <img src="{{ Storage::url(user()->picture->id) }}" class="w-8 h-8 rounded-full" alt="{{ user()->name }}" title="{{ user()->name }}">
            @else
                <span name="account_circle" class="material-icons-outlined w-8 h-8 flex items-center justify-center text-purple text-2xl pointer-events-none" alt="{{ user()->name }}" title="{{ user()->name }}">account_circle</span>
            @endif

            @stack('navbar_profile_welcome')

            <div class="flex flex-col text-black ml-2">
                <span class="text-xs">{{ trans('general.welcome') }}</span>

                {{ user()->name }}
            </div>
        </div>

        @stack('profile_menu_profile_start')

        <livewire:menu.profile />

        @stack('profile_menu_profile_end')
    </div>

    @stack('profile_menu_end')

    @can('read-notifications')
    @stack('notifications_menu_start')

    <div class="notifications-menu user-menu menu-list fixed h-full ltr:-left-80 rtl:-right-80">
        @stack('notifications_menu_title_start')

        <div class="flex items-center mb-3">
            <span name="notifications" class="material-icons-outlined w-8 h-8 flex items-center justify-center text-purple text-2xl pointer-events-none">notifications</span>

            <div class="text-black ltr:ml-1 rtl:mr-1">
                {{ trans_choice('general.your_notifications', 2) }}
            </div>
        </div>

        @stack('notifications_menu_notifications_start')

        <livewire:menu.notifications />

        @stack('notifications_menu_notifications_end')
    </div>

    @stack('notifications_menu_end')
    @endcan

    @stack('settings_menu_start')

    <div class="settings-menu user-menu menu-list fixed h-full overflow-y-unset ltr:-left-80 rtl:-right-80">
        @stack('settings_menu_title_start')

        <div class="flex items-center mb-3">
            <span name="settings" class="material-icons-outlined w-8 h-8 flex items-center justify-center text-purple text-2xl pointer-events-none">settings</span>

            <div class="text-black ltr:ml-1 rtl:mr-1">
                {{ trans_choice('general.settings', 2) }}
            </div>
        </div>

        @stack('settings_menu_settings_start')

        <livewire:menu.settings />

        @stack('settings_menu_settings_end')
    </div>

    @stack('settings_menu_end')

    @stack('add_new_menu_start')

    <div class="add-new-menu user-menu menu-list fixed h-full ltr:-left-80 rtl:-right-80">
        @stack('add_new_menu_title_start')

        <div class="flex items-center mb-3">
            <span name="add_circle_outline" class="material-icons-outlined w-8 h-8 flex items-center justify-center text-purple text-2xl pointer-events-none">add_circle_outline</span>

            <div class="text-black ltr:ml-1 rtl:mr-1">
                {{ trans('general.new_more') }}
            </div>
        </div>

        @stack('add_new_menu_add_new_start')

        <livewire:menu.neww />

        @stack('add_new_menu_add_new_end')
    </div>

    @stack('add_new_menu_end')

    <button type="button" class="toggle-button absolute ltr:-right-2 rtl:-left-2 top-8 cursor-pointer transition-opacity ease-in-out z-50">
        <span class="material-icons text-lg text-purple transform ltr:rotate-90 rtl:-rotate-90 pointer-events-none">expand_circle_down</span>
    </button>

    <span data-menu-close id="menu-cancel" class="material-icons absolute ltr:-right-2 rtl:-left-1.5 transition-all top-8 text-lg text-purple cursor-pointer z-10 hidden">cancel</span>

    <div class="fixed w-full h-full invisible lg:hidden js-menu-background" style="background-color: rgba(0, 0, 0, 0.5); z-index: -1;"></div>
</div>

@stack('menu_end')
