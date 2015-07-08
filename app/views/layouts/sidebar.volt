<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="treeview <?php echo $this->router->getControllerName()=='menu'?'active':'' ?>">
        <a href="#"><i class="fa fa-sitemap"></i> <span>Quản lý Menus</span></a>
        <ul class="treeview-menu">
            <li class="<?php echo $this->router->getActionName()==''?'active':'' ?>"><?php echo $this->tag->linkTo(array("menu", '<i class="fa fa-angle-double-right"></i>Danh sách menus')); ?></li>
            <li class="<?php echo $this->router->getActionName()=='new'?'active':'' ?>"><?php echo $this->tag->linkTo(array("menu/new", '<i class="fa fa-angle-double-right"></i>Tạo Menu')); ?></li>
        </ul>
    </li>
    <li class="treeview <?php echo $this->router->getControllerName()=='category'?'active':'' ?>">
        <a href="#"><i class="fa fa-briefcase"></i> <span>Quản lý chuyên đề</span></a>
        <ul class="treeview-menu">
            <li class="<?php echo $this->router->getActionName()==''?'active':'' ?>"><?php echo $this->tag->linkTo(array("category", '<i class="fa fa-angle-double-right"></i>Danh sách chuyên đề')); ?></li>
            <li class="<?php echo $this->router->getActionName()=='new'?'active':'' ?>"><?php echo $this->tag->linkTo(array("category/new", '<i class="fa fa-angle-double-right"></i>Tạo chuyên đề')); ?></li>
        </ul>
    </li>
    <li class="treeview <?php echo $this->router->getControllerName()=='books'?'active':'' ?>">
        <a href="#"><i class="fa fa-book"></i> <span>Quản lý sách</span></a>
        <ul class="treeview-menu">
            <li><?php echo $this->tag->linkTo(array("books", '<i class="fa fa-angle-double-right"></i>Danh sách sách')); ?></li>
            <li><?php echo $this->tag->linkTo(array("books/new", '<i class="fa fa-angle-double-right"></i>Tạo sách mới')); ?></li>
        </ul>
    </li>
    <li class="">
        <?php echo $this->tag->linkTo(array("users", '<i class="fa fa-users"></i> <span>Quản lý người dùng</span>')); ?>
    </li>
    <li class="treeview <?php echo ($this->router->getControllerName()=='roles' || $this->router->getControllerName()=='permissions')?'active':'' ?>">
            <a href="#"><i class="fa fa-book"></i> <span>Quản lý phân quyền</span></a>
            <ul class="treeview-menu">
                <li><?php echo $this->tag->linkTo(array("roles", '<i class="fa fa-angle-double-right"></i> Quản lý quyền')); ?></li>
                <li><?php echo $this->tag->linkTo(array("permissions", '<i class="fa fa-angle-double-right"></i> Phân quyền')); ?></li>
            </ul>
        </li>
</ul>