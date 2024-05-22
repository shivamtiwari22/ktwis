<div class="leftside-menu">

    <!-- LOGO -->
    <a href="index.html" class="logo text-center logo-light">
        <span class="logo-lg">
            <!-- <img src="{{ asset('public/assets/images/logo.png') }}" alt="" height="16"> -->
            <label for="">Ktwis</label>
        </span>
        <span class="logo-sm">
            <!-- <img src="{{ asset('public/assets/images/logo_sm.png') }}" alt="Hello" height="16"> -->
            <label for="">Ktwis</label>
        </span>
    </a>



    <div class="h-100" id="leftside-menu-container" data-simplebar="">

        <!--- Sidemenu -->
        <ul class="side-nav">

            <!-- <li class="side-nav-title side-nav-item"> {{ __('messages.navigation')  }}</li> -->
            <li class="side-nav-item">
                <a href="{{ route('vendor.dashboard') }}" class="side-nav-link">
                    <i class="uil-comments-alt"></i>
                    <span>{{ __('messages.dashboard') }}</span>
                </a>
            </li>
            <li class="side-nav-item">
                <a href="{{ route('vendor.wallet.index') }}" class="side-nav-link">
                    <i class="uil-wallet"></i>
                    <span>{{ __('messages.wallet') }}</span>
                    {{-- <span>Wallet</span>/ --}}
                </a>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCatalogs" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="uil-tag-alt"></i>
                    <span>{{ __('messages.catalog')  }}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarCatalogs">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.products.index') }}"><i class="uil-angle-double-right"></i>{{ __('messages.products') }} </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.attributes.index') }}"><i class="uil-angle-double-right"></i>
                                {{ __('messages.attribute') }}</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarstocksCatalogs" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="uil-box"></i>
                    <span>{{ __('messages.stocks')  }} </span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarstocksCatalogs">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.inventory.index') }}"><i class="uil-angle-double-right"></i>{{ __('messages.inventory') }} </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDashboards" aria-expanded="false" aria-controls="sidebarDashboards" class="side-nav-link">
                    <i class="uil-car"></i>
                    <span>{{ __('messages.shipping')  }}    <span class="menu-arrow"></span> </span>
                </a>
                <div class="collapse" id="sidebarDashboards">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.carrier.list') }}">{{ __('messages.carriers') }} </a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('vendor.shipping.rates') }}"> {{ __('messages.shipping_rates') }}</a>
                        </li> --}}
                        <li>
                            <a href="{{ route('vendor.zones.index') }}">{{ __('messages.zone') }}</a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebar" aria-expanded="false" aria-controls="sidebar" class="side-nav-link">
                    <i class="uil-comment-alt-message"></i>

                    <span>{{ __('messages.twillio')  }} </span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebar">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('twilio.get.sendsms') }}"><span>{{ __('messages.sms')  }}</span></a>
                        </li>
                    </ul>
                </div>
            </li> --}}

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarsupport" aria-expanded="false" aria-controls="sidebar" class="side-nav-link">
                    <i class="uil-headphones"></i>
                    <span class="badge bg-success float-end"></span>
                    {{-- <span>Support Desk</span> --}}
                 <span>{{ __('messages.Support Desk')  }}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarsupport">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{route('vendor.message.message_index')}}">{{ __('messages.Message')  }} </a>
                        </li>
                    </ul>
                </div>

                <div class="collapse" id="sidebarsupport">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.disputes.index') }}"> {{ __('messages.Disputes')  }} </a>
                        </li>
                    </ul>
                </div>
               
            </li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarcoupons" aria-expanded="false" aria-controls="sidebarcoupons" class="side-nav-link">
                    <i class="uil-gift"></i>

                    <span>{{ __('messages.coupons')  }}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarcoupons">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.coupon.list') }}">{{ __('messages.coupons') }}</a>
                        </li>
                    </ul>
                </div>
            </li>

            <hr>
            <li class="side-nav-title side-nav-item">{{ __('messages.apps')  }}</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarcustomer" aria-expanded="false" aria-controls="sidebarcoupons" class="side-nav-link">
                    <i class="uil-user"></i>
                    <span>{{__('messages.CustomerPanel')}}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarcustomer">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.carts.index') }}">{{ __('messages.Carts')  }}</a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.wishlist.index') }}">{{ __('messages.Wishlist')  }}</a>
                        </li>
                        <li>
                            <a href="{{route('vendor.order.index')}}">{{ __('messages.Orders')  }}</a>
                        </li>

                        <li>
                            <a href="{{route('vendor.cancel')}}">Cancellation</a>
                        </li>

                        <li>
                            <a href="{{route('vendor.customer.index')}}">Customer</a>
                        </li>
                     
                    </ul>
                </div>
            </li>


            <li class="side-nav-item">
                <a href="{{route('vendor.message.message_index') }}" class="side-nav-link">
                    <i class="uil-comment-alt-message"></i>
                    <span>{{ __('messages.Message')  }}</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('vendor.disputes.index') }}" class="side-nav-link">
                    <i class="uil-tag-alt"></i>
                    <span> {{ __('messages.Disputes')  }} </span>
                </a>
            </li>


            <li class="side-nav-item">
                <a href="{{ route('vendor.reviews.index') }}" class="side-nav-link">
                    <i class="uil-eye"></i>
                    <span>{{ __('messages.reviews')  }}</span>
                </a>
            </li>

            {{-- <li class="side-nav-item">
                <a href="{{ route('vendor.push-notificaiton') }}" class="side-nav-link">
                    <i class="uil-eye"></i>
                    <span>{{ __('messages.Notification')  }}</span>
                
                </a>
            </li> --}}


           

            {{-- <li class="side-nav-item">
                <a href="{{ route('vendor.invoice.index') }}" class="side-nav-link">
                    <i class="uil-bill"></i>
                    <span>{{ __('messages.invoices')  }}</span>
                </a>
            </li> --}}

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarsettings" aria-expanded="false" aria-controls="sidebarsettings" class="side-nav-link">
                    <i class="uil-cog"></i>
                    <span>{{ __('messages.settings')  }}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarsettings">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('vendor.settings.tax.index') }}">{{ __('messages.tax') }}</a>
                        </li>
                        <li>
                            <a href="{{route('vendor.settings.shops.create')}}">{{ __('messages.shop setting')  }}</a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.specifications') }}"> {{ __('messages.Specifications')  }} </a>
                        </li>
                        <li>
                            <a href="{{ route('vendor.return_cancellation') }}"> {{ __('messages.Return & Cancellation Policy')  }}  </a>
                        </li>
                    </ul>
                </div>
              
            </li>

            {{-- <li class="side-nav-item">
                <a href="{{ route('vendor.currency.index') }}" class="side-nav-link">
                    <i class="uil-dollar-sign"></i>
                    <span>{{ __('messages.Conversion')  }}</span>
                </a>
            </li> --}}


            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarreports" aria-expanded="false" aria-controls="sidebarreports" class="side-nav-link">
                    <i class="uil-analytics"></i>
                    <span>{{ __('messages.reports')  }}</span>
                    <span class="menu-arrow"></span>

                </a>
                <div class="collapse" id="sidebarreports">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{route('vendor.performance.index')}}">{{ __('messages.performance')  }}</a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- Help Box -->
    <div class="help-box text-white text-center">
        <a href="javascript: void(0);" class="float-end close-btn text-white">
            <i class="mdi mdi-close"></i>
        </a>
        <img src="{{ asset('public/assets/images/help-icon.svg') }}" height="90" alt="Helper Icon Image">
        <h5 class="mt-3">Unlimited Access</h5>
        <p class="mb-3">Upgrade to plan to get access to unlimited reports</p>
        <a href="javascript: void(0);" class="btn btn-outline-light btn-sm">Upgrade</a>
    </div>
    <!-- end Help Box -->
    <!-- End Sidebar -->

    <div class="clearfix"></div>

</div>