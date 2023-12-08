<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ env('APP_NAME') }} | Dashboard</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="description" content="Developed By M Abdur Rokib Promy">
    <meta name="keywords" content="Admin, Bootstrap 3, Template, Theme, Responsive">
    
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css" />
    <link href="/assets/css/datepicker/datepicker3.css" rel="stylesheet" type="text/css" />
    <link href="/assets/js/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="/assets/js/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
          <![endif]-->

    <!-- jQuery -->
    <script src="/assets/js/jquery.min.js" type="text/javascript"></script>
    <!-- Bootstrap -->
    <script src="/assets/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- Bootbox -->
    <script src="/assets/js/bootbox.min.js" type="text/javascript"></script>
    <!-- Datepicker -->
    <script src="/assets/js/plugins/datepicker/bootstrap-datepicker.js" type="text/javascript"></script>
    <!-- Select2 -->
    <script src="/assets/js/plugins/select2/js/select2.min.js" type="text/javascript"></script>
</head>
<body class="skin-black">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="{{ url('/') }}" class="logo">
                {{ env('APP_NAME') }}
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <div class="navbar-right">
                            <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/profile/view') }}"><i class="fa fa-btn fa-user"></i>Profile</a></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
                        </div>
                    </nav>
                </header>
                <div class="wrapper row-offcanvas row-offcanvas-left">
                    <!-- Left side column. contains the logo and sidebar -->
                    <aside class="left-side sidebar-offcanvas">
                        <!-- sidebar: style can be found in sidebar.less -->
                         @if (Auth::guest())

                         @else
                        <section class="sidebar">
                            <!-- search form -->
                            <form action="#" method="get" class="sidebar-form">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control" placeholder="Search..."/>
                                    <span class="input-group-btn">
                                        <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                                    </span>
                                </div>
                            </form>
                            <!-- /.search form -->
                            <!-- sidebar menu: : style can be found in sidebar.less -->
                            <ul class="sidebar-menu">
                                <li class="active">
                                    <a href="{{ url('/') }}">
                                        <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ url('/queue') }}">
                                        <i class="fa fa-gavel"></i> <span>New Search</span>
                                    </a>
                                </li>
                                 @if (Auth::user()->is_admin()) 
                                <li>
                                    <a href="{{ url('/user/list') }}">
                                        <i class="fa fa-users"></i> <span>Users</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </section>
                        <!-- /.sidebar -->
                         @endif
                    </aside>

                    <aside class="right-side">

                        <!-- Main content -->
                        <section class="content">
                             @if(isset($alert)) 
                                <div class="alert {{ $alert['class'] }}" style='margin: 20px 15px;'>
                                <i class="fa {{ $alert['icon'] }}"></i>&nbsp;
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                    @if(preg_match("/info/", $alert['class'])) 
                                        <strong>Heads up!</strong><br>
                                    @elseif(preg_match("/success/", $alert['class'])) 
                                        <strong>Well done!</strong><br>
                                    @elseif(preg_match("/warning/", $alert['class']))
                                        <strong>Warning!</strong><br>
                                    @elseif(preg_match("/danger/", $alert['class']))
                                        <strong>Application error!</strong><br>
                                    @endif
                                    {{ $alert['text'] }}<br>
                                </div>
                            @endif   
                            @yield('content')
                        </section>
                        
                    </aside><!-- /.right-side -->

        </div><!-- ./wrapper -->
</body>
</html>