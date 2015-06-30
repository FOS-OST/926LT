<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="treeview <?php echo $this->router->getControllerName()=='menu'?'active':'' ?>">
        <a href="#"><i class="fa fa-sitemap"></i> <span>Menus</span></a>
        <ul class="treeview-menu">
            <li class="<?php echo $this->router->getActionName()==''?'active':'' ?>"><?php echo $this->tag->linkTo(array("menu", '<i class="fa fa-angle-double-right"></i>List Menu')); ?></li>
            <li class="<?php echo $this->router->getActionName()=='new'?'active':'' ?>"><?php echo $this->tag->linkTo(array("menu/new", '<i class="fa fa-angle-double-right"></i>Create Menu')); ?></li>
        </ul>
    </li>
    <li class="treeview <?php echo $this->router->getControllerName()=='category'?'active':'' ?>">
        <a href="#"><i class="fa fa-briefcase"></i> <span>Topics</span></a>
        <ul class="treeview-menu">
            <li class="<?php echo $this->router->getActionName()==''?'active':'' ?>"><?php echo $this->tag->linkTo(array("category", '<i class="fa fa-angle-double-right"></i>List Topic')); ?></li>
            <li class="<?php echo $this->router->getActionName()=='new'?'active':'' ?>"><?php echo $this->tag->linkTo(array("category/new", '<i class="fa fa-angle-double-right"></i>Create Topic')); ?></li>
        </ul>
    </li>
    <li class="treeview <?php echo $this->router->getControllerName()=='books'?'active':'' ?>">
        <a href="#"><i class="fa fa-book"></i> <span>Books Management</span></a>
        <ul class="treeview-menu">
            <li><?php echo $this->tag->linkTo(array("books", '<i class="fa fa-angle-double-right"></i>List Book')); ?></li>
            <li><?php echo $this->tag->linkTo(array("books/new", '<i class="fa fa-angle-double-right"></i>Create Book')); ?></li>
        </ul>
    </li>
    <li class="">
        <?php echo $this->tag->linkTo(array("users", '<i class="fa fa-users"></i> <span>Users</span>')); ?>
    </li>
    <li class="treeview <?php echo ($this->router->getControllerName()=='roles' || $this->router->getControllerName()=='permissions')?'active':'' ?>">
            <a href="#"><i class="fa fa-book"></i> <span>Permissions Management</span></a>
            <ul class="treeview-menu">
                <li><?php echo $this->tag->linkTo(array("roles", '<i class="fa fa-angle-double-right"></i> Roles')); ?></li>
                <li><?php echo $this->tag->linkTo(array("permissions", '<i class="fa fa-angle-double-right"></i> Permissions')); ?></li>
            </ul>
        </li>
</ul>