<!DOCTYPE html lang="vi">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/font-awesome.min.css" type="text/css" />
        <link rel="stylesheet" href="/css/ionicons.min.css" type="text/css" />
        <link rel="stylesheet" href="/css/admin.css" type="text/css" />
        <link rel="stylesheet" href="/css/datatables/dataTables.bootstrap.css" type="text/css" />
        <link rel="stylesheet" href="/css/daterangepicker/daterangepicker-bs3.css" type="text/css" />
        <link rel="stylesheet" href="/js/plugins/jquery.alert/alertify.core.css" type="text/css" />
        {{ assets.outputCss() }}

        <title>Ebooks</title>
        <script src="/js/jquery-2.1.4.min.js" type="text/javascript"></script>
        <script src="/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="/js/plugins/jquery.alert/alertify.min.js" type="text/javascript"></script>
        <script src="/js/ckeditor/ckeditor.js"></script>
        <script src="/js/ckeditor/adapters/jquery.js"></script>
        <script src="/js/ckfinder/ckfinder.js" type="text/javascript"></script>
        {{ assets.outputJs() }}
    </head>
    <body class="skin-blue">
        <header class="header">
            <a href="index.html" class="logo">Ebooks</a>
            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span>{{identity['name']}} <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="/img/avatar5.png" class="img-circle" alt="User Image" />
                                    <p>
                                        {{identity['name']}}
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-right">
                                        <a href="./auth/logout" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="/img/avatar5.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p>Hi, {{identity['name']}}</p>

                            <a href="#"><i class="fa fa-circle text-success"></i> {{identity['email']}}</a>
                        </div>
                    </div>
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
                    {% include "layouts/sidebar.volt" %}
                </section>
            </aside>

            <aside class="right-side">
                <section class="content-header">
                    <h1>
                        {{title}}
                        <small></small>
                    </h1>
                    {% if bc is defined %}
                    <ol class="breadcrumb">
                        {% for breadcrumb in bc %}
                        <li class="active"><i class="fa"></i> <a href="{{ breadcrumb['link'] }}">{{ breadcrumb['text'] }}</a></li>
                        {% endfor %}
                    </ol>
                    {% endif %}
                </section>

                <!-- Main content -->
                <section class="content">
                    {{ content() }}
                </section>
            </aside>
        </div>
    </body>
    <footer>
        <script src="/js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <script src="/js/app.js" type="text/javascript"></script>
    </footer>
</html>
