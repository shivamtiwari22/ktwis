<div class="leftside-menu">

    <!-- LOGO -->
    @php
        use App\Models\SystemSettings;
        
        $system = SystemSettings::where('user_id', Auth::user()->id)->first();
    @endphp
    <a href="{{route('dashboard')}}" class="logo text-center logo-light">
        <span class="logo-lg">
            <img src=" {{$system ? asset('public/admin/system/'. $system->brand_logo) :  asset('public/assets/images/logo.png') }}" alt="" height="16">

        </span>


        <span class="logo-sm">
            <img src="{{ asset('public/assets/images/logo_sm.png') }}" alt="Hello" height="16">
        </span>
    </a>


    <!-- LOGO -->
    <a href="index.html" class="logo text-center logo-dark">
        <span class="logo-lg">
            {{-- <img src="{{ asset('public/assets/images/logo-dark.png') }}" alt="" height="16"> --}}
        </span>
        <span class="logo-sm">
            {{-- <img src="{{ asset('public/assets/images/logo_sm_dark.png') }}" alt="" height="16"> --}}
        </span>
    </a>

    <div class="h-100" id="leftside-menu-container" data-simplebar="">

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title side-nav-item">Navigation</li>
            <li class="side-nav-item">
                <a href="{{ route('dashboard') }}" class="side-nav-link">
                    <i class="uil-home-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            {{-- <li class="side-nav-item">
                <a href="{{ route('admin.wallet.index') }}" class="side-nav-link">
                    <i class="uil-wallet"></i>
                    <span>Wallet</span>
                </a>
            </li> --}}
            <li class="side-nav-item">
                <a href="{{ route('contact.user.list') }}" class="side-nav-link">
                    <i class="uil-comments-alt"></i>
                    <span>Contact list</span>
                </a>
            </li>
           
            <li class="side-nav-item">
                <a href="{{ route('admin.order') }}" class="side-nav-link">
                    <i class="fa fa-cart-plus f-size" ></i> 
                    <span>Order</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('admin.wishlist') }}" class="side-nav-link">
                    <i class="fa fa-heart f-size"></i> 
                    <span>Wishlist</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('admin.cart') }}" class="side-nav-link">
                    <i class="fa fa-cart-plus f-size"></i> 
                    <span>Cart</span>
                </a>
            </li>


            <li class="side-nav-item">
                <a href="{{ route('admin.cancel') }}" class="side-nav-link">
                    <i class="fa fa-ban f-size"></i>    
                    <span>Cancellation</span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="{{ route('admin.customer.index') }}" class="side-nav-link">
                    <i class="uil-user f-size"></i>  
                    <span>Customer</span>
                </a>
            </li>


            <li class="side-nav-title side-nav-item">Apps</li>

            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarCategories" aria-expanded="false"
                    aria-controls="sidebarCategories" class="side-nav-link">
                    <i class="uil-clipboard-alt"></i>
                    <span>Categories</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarCategories">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.categories.create') }}">Add New</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.list') }}">Category List</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarappereance" aria-expanded="false"
                    aria-controls="sidebarappereance" class="side-nav-link">
                    <i class="uil-clipboard-alt"></i>
                    <span>Appereance</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarappereance">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.appereance.pages') }}">Page</a>
                        </li>
                
                   
                        <li>
                            <a href="{{ route('admin.appereance.seo.pages') }}">Seo Page</a>
                        </li>
                  
                    {{-- <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.appereance.blogs') }}">Blogs</a>
                        </li>
                    </ul>
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.appereance.templates') }}">Email Templates</a>
                        </li>
                    </ul> --}}
                  
                        <li>
                            <a href="{{ route('admin.appereance.faq') }}">FAQ</a>
                        </li>
                  
               
                        <li>
                            <a href="{{ route('admin.appereance.content') }}">Dynamic Content</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebaproducttype" aria-expanded="false"
                    aria-controls="sidebarappereance" class="side-nav-link">
                    <i class="uil-clipboard-alt"></i>
                    <span>Product Type</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebaproducttype">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.producttype.index') }}">Product Type</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarVendorApplication" aria-expanded="false"
                    aria-controls="sidebarVendorApplication" class="side-nav-link">
                    <i class="uil-clipboard-alt"></i>
                    <span>Vendors </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarVendorApplication">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('admin.vendor.applications') }}">Merchants</a>
                        </li>
                        {{-- <li>
                            <a href="{{ route('admin.vendor.rejected.applications') }}">Rejected Applications</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vendor.pending.applications') }}">Pending Applications</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.vendor.publish.applications') }}">Published Applications</a>
                        </li> --}}
                    </ul>
                </div>
            </li>
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarDropdown" aria-expanded="false"
                    aria-controls="sidebarDropdown" class="side-nav-link">
                    <i class="uil-cog"></i>
                    <span>Settings</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarDropdown">
                    <ul class="side-nav-second-level">
                        <li>
                            <a href="{{ route('currencies.list') }}">All Currencies</a>
                           
                        </li>
                        <li>
                            <a href="{{ route('business.list') }}">All Business Area</a>
                           
                        </li>
                        {{-- <li>
                            <a href="{{ route('commision.list') }}">All Commisions</a>

                        </li> --}}
                        {{-- <li>
                            <a href="{{ route('languages.list') }}">All Languages</a>
                           
                            
                        </li> --}}

                        <li>
                            <a href="{{ route('admin.setting.sale_banner') }}">Sale banner</a>
                        </li>

                        <li>
                            <a href="{{ route('admin.system_setting') }}">System Settings</a>
                        </li>

                        <li>
                            <a href="{{ route('admin.global_setting') }}">Global Settings</a>
                        </li>

                        <li>
                            <a href="{{ route('admin.dispute_text') }}">Dispute Text</a>
                        </li>

                        <li>
                            <a href="{{ route('admin.client.lists') }}">Clients</a>
                        </li>
                        
                        <li>
                            <a href="{{ route('admin.setting.charges') }}">Set Charges</a>
                        </li>

            </li>
        </ul>
    </div>
    </li>

    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#sidebarsupport" aria-expanded="false" aria-controls="sidebar"
            class="side-nav-link">
            <i class="uil-home-alt"></i>
            <span class="badge bg-success float-end"></span>
            <span>Support Desk</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="sidebarsupport">
            <ul class="side-nav-second-level">
            {{-- <ul class="side-nav-second-level">
                <li>
                    <a href="{{ route('admin.disputes.Ticket') }}">Tickets </a>
                </li>
            </ul> --}}
            {{-- </div>  
    <div class="collapse" id="sidebarsupport"> --}}
          
                <li>
                    <a href="{{ route('admin.disputes.index') }}">Disputes </a>
                </li>
        
   
                <li>
                    <a href="{{ route('admin.test.test_monial') }}">Testimonial </a>
                </li>
         
         
                <li>
                    <a href="{{ route('admin.refund.refund_datas') }}">Refunds </a>
                </li>
        
                <li>
                    <a href="{{ route('admin.notify') }}">Notification</a>
                </li>
      
        </div>

    </li>
    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#sidebarslider" aria-expanded="false" aria-controls="sidebarslider"
            class="side-nav-link">
            <i class="uil-film"></i>
            <span>Slider</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="sidebarslider">
            <ul class="side-nav-second-level">
                <li>
                    <a href="{{ route('slider.list') }}">All Sliders</a>
                </li>
            </ul>
            {{-- <ul class="side-nav-second-level">
                <li>
                    <a href="{{ route('slider.create') }}">Add Slider</a>
                </li>
            </ul> --}}
        </div>
    </li>

    <li class="side-nav-item">
        <a href="{{ route('admin.reviews.index') }}" class="side-nav-link">
            <i class="uil-eye"></i>
            <span>{{ __('messages.reviews') }}</span>
        </a>
    </li>

    <li class="side-nav-item">
        <a data-bs-toggle="collapse" href="#sidebarreports" aria-expanded="false" aria-controls="sidebarreports"
            class="side-nav-link">
            <i class="uil-analytics"></i>
            <span>{{ __('messages.reports') }}</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse" id="sidebarreports">
            <ul class="side-nav-second-level">
               
                <li>
                    <a href="{{ route('admin.performance.index') }}">Performance</a>
                </li>
                <li>
                    <a href="{{ route('admin.payoutDetails') }}">Payout Details<a>
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
