

<div class="navbar-custom">
    <ul class="list-unstyled topbar-menu float-end mb-0">
        <li class="dropdown notification-list d-lg-none">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                aria-haspopup="false" aria-expanded="false">
                <i class="dripicons-search noti-icon"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-animated dropdown-lg p-0">
                <form class="p-3">
                    <input type="text" class="form-control" placeholder="Search ..."
                        aria-label="Recipient's username">
                </form>
            </div>
        </li>
        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                aria-haspopup="false" aria-expanded="false">
                {{-- <span class="align-middle d-none d-sm-inline-block">English</span> <i class="mdi mdi-chevron-down d-none d-sm-inline-block align-middle"></i> --}}
                @php
                    $language = Config::get('languages')[App::getLocale()];
                @endphp

                @if ($language == 'English')
                    <img src="{{ asset('public/assets/images/flags/us.jpg') }}" alt="user-image" class="me-1"
                    height="12">
                    @elseif($language == 'Amharic')
                    <img src="{{ asset('public/assets/images/flags/ethopia.png') }}" alt="Flag" class="me-1"
                    height="12">
                @elseif ($language == 'French')
                    <img src="{{ asset('public/assets/images/flags/france.jpg') }}" alt="Flag" class="me-1"
                        height="12">
                @else
                    <img src="{{ asset('public/assets/images/flags/Flag-Saudi-Arabia.webp') }}" alt="Flag"
                        class="me-1" height="12">
                @endif
                {{ Config::get('languages')[App::getLocale()] }}
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu">

                <!-- item-->
                {{-- <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <img src="{{asset('public/assets/images/flags/germany.jpg')}}" alt="user-image" class="me-1" height="12"> <span class="align-middle">German</span>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <img src="{{asset('public/assets/images/flags/italy.jpg')}}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Italian</span>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <img src="{{asset('public/assets/images/flags/spain.jpg')}}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Spanish</span>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <img src="{{asset('public/assets/images/flags/russia.jpg')}}" alt="user-image" class="me-1" height="12"> <span class="align-middle">Russian</span>
                </a> --}}


                @foreach (Config::get('languages') as $lang => $language)
                    @if ($lang != App::getLocale())
                        <a class="dropdown-item" href="{{ url('vendor/lang/' . $lang) }}">
                            @if ($lang == 'en')
                                <img src="{{ asset('public/assets/images/flags/us.jpg') }}" alt="user-image"
                                    class="me-1" height="12">
                                    @elseif($lang == "ep")
                                    <img src="{{ asset('public/assets/images/flags/ethopia.png') }}" alt="Flag" class="me-1"
                                    height="12">
                            @elseif ($lang == 'fr')
                                <img src="{{ asset('public/assets/images/flags/france.jpg') }}" alt="user-image"
                                    class="me-1" height="12">
                                   
                            @else
                                <img src="{{ asset('public/assets/images/flags/Flag-Saudi-Arabia.webp') }}"
                                    alt="user-image" class="me-1" height="12">
                            @endif
                            {{ $language }}
                        </a>
                    @endif
                @endforeach

            </div>
        </li>

        @php   $count =  auth()->user()->unreadNotifications->count();   
                      @endphp

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button"
                aria-haspopup="false" aria-expanded="false">
                <i class="dripicons-bell noti-icon"></i>
                <span class="{{$count > 0 ?  'noti-icon-badge' : ''}} "></span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg">
                <!-- item-->
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">
                        <span class="float-end">
                            <a href="{{route('vendor.allMarkAsRead')}}" class="text-dark">
                            <small>{{$count > 0 ?  'Clear All' : ''}}  </small>
                            </a>
                        </span>Notification
                    </h5>
                </div>

                <div style="max-height: 230px;" data-simplebar="">
                    @php
                        use Carbon\Carbon;
                    @endphp

                    @foreach (auth()->user()->unreadNotifications as $notification)
                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                            <div class="notify-icon bg-primary">
                                <i class="mdi mdi-comment-account-outline"></i>
                            </div>
                            <p class="notify-details">{{ $notification->data['message'] }}
                                <small
                                    class="text-muted">{{ Carbon::parse($notification->created_at)->diffForHumans(null, true) . ' ago' }}</small>
                            </p>
                        </a>
                    @endforeach
                </div>

                <!-- All-->
                <a href="{{route('vendor.viewAll')}}" class="dropdown-item text-center text-primary notify-item notify-all">
                    {{$count > 0 ?  'View All' : ''}}  
                </a>

            </div>
        </li>

        <li class="dropdown notification-list d-none d-sm-inline-block">
            <!-- <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="dripicons-view-apps noti-icon"></i>
            </a> -->
            <!-- <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg p-0">

                <div class="p-2">
                    <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/slack.png') }}" alt="slack">
                                <span>Slack</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/github.png') }}" alt="Github">
                                <span>GitHub</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/dribbble.png') }}" alt="dribbble">
                                <span>Dribbble</span>
                            </a>
                        </div>
                    </div>

                    <div class="row g-0">
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/bitbucket.png') }}" alt="bitbucket">
                                <span>Bitbucket</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/dropbox.png') }}" alt="dropbox">
                                <span>Dropbox</span>
                            </a>
                        </div>
                        <div class="col">
                            <a class="dropdown-icon-item" href="#">
                                <img src="{{ asset('public/assets/images/brands/g-suite.png') }}" alt="G Suite">
                                <span>G Suite</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div> -->
        </li>

        {{-- <li class="notification-list">
            <a class="nav-link end-bar-toggle" href="javascript: void(0);">
                <i class="dripicons-gear noti-icon"></i>
            </a>
        </li> --}}

        <?php
        use Illuminate\Support\Facades\Auth;
        $user = Auth::user();
        $roles = $user->roles->first();
        ?>

        <li class="dropdown notification-list">
            <a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#"
                role="button" aria-haspopup="false" aria-expanded="false">

                <span class="account-user-avatar">
                    @if ($user->profile_pic)
                        <img src="{{ asset(asset('public/vendor/profile_pic/' . $user->profile_pic)) }} "
                            alt="user-image" class="rounded-circle">
                    @else
                        <img src="{{ asset('public/assets/images/users/avatar-1.jpg') }}" alt="user-image"
                            class="rounded-circle">
                    @endif
                </span>
                <span>

                    <span class="account-user-name">{{ $user->name }}</span>
                    <span class="account-position">{{ $roles->role }}</span>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
                <!-- item-->
                <div class=" dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome !</h6>
                </div>

                <!-- item-->
                <a href="{{ route('vendor.profile') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-account-circle me-1"></i>
                    <span>My Account</span>
                </a>

                <!-- item-->
                <a href="{{ route('vendor.change.password') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-lock-outline me-1"></i>
                    <span>Change Password</span>
                </a>

                <!-- item-->
                <a href="{{ route('vendor.logout') }}" class="dropdown-item notify-item text-danger">
                    <i class="mdi mdi-logout me-1"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li>

    </ul>
    <button class="button-menu-mobile open-left">
        <i class="mdi mdi-menu"></i>
    </button>
    <div class="app-search dropdown d-none d-lg-block">
        {{-- <form>
            <div class="input-group">
                <input type="text" class="form-control dropdown-toggle" placeholder="Search..." id="top-search">
                <span class="mdi mdi-magnify search-icon"></span>
                <button class="input-group-text btn-primary" type="submit">Search</button>
            </div>
        </form> --}}

        <div class="dropdown-menu dropdown-menu-animated dropdown-lg" id="search-dropdown">
            <!-- item-->
            <div class="dropdown-header noti-title">
                <h5 class="text-overflow mb-2">Found <span class="text-danger">17</span> results</h5>
            </div>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <i class="uil-notes font-16 me-1"></i>
                <span>Analytics Report</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <i class="uil-life-ring font-16 me-1"></i>
                <span>How can I help you?</span>
            </a>

            <!-- item-->
            <a href="javascript:void(0);" class="dropdown-item notify-item">
                <i class="uil-cog font-16 me-1"></i>
                <span>User profile settings</span>
            </a>

            <!-- item-->
            <div class="dropdown-header noti-title">
                <h6 class="text-overflow mb-2 text-uppercase">Users</h6>
            </div>

            <div class="notification-list">
                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <div class="d-flex">
                        <img class="d-flex me-2 rounded-circle"
                            src="{{ asset('public/assets/images/users/avatar-5.jpg') }}"
                            alt="Generic placeholder image" height="32">
                        <div class="w-100">
                            <h5 class="m-0 font-14">Erwin Brown</h5>
                            <span class="font-12 mb-0">UI Designer</span>
                        </div>
                    </div>
                </a>

                <!-- item-->
                <a href="javascript:void(0);" class="dropdown-item notify-item">
                    <div class="d-flex">
                        <img class="d-flex me-2 rounded-circle"
                            src="{{ asset('public/assets/images/users/avatar-5.jpg') }}"
                            alt="Generic placeholder image" height="32">
                        <div class="w-100">
                            <h5 class="m-0 font-14">Jacob Deo</h5>
                            <span class="font-12 mb-0">Developer</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
