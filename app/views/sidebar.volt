<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
    <li class="treeview active">
        <a href="#"><i class="fa fa-briefcase"></i> <span>Categories</span></a>
        <ul class="treeview-menu">
            <li><?php echo $this->tag->linkTo(array("category", '<i class="fa fa-angle-double-right"></i>List category')); ?></li>
            <li><?php echo $this->tag->linkTo(array("category/create", '<i class="fa fa-angle-double-right"></i>Create category')); ?></li>
        </ul>
    </li>
    <li class="treeview active">
        <a href="#"><i class="fa fa-book"></i> <span>Books Management</span></a>
        <ul class="treeview-menu">
            <li><?php echo $this->tag->linkTo(array("books", '<i class="fa fa-angle-double-right"></i>List Book')); ?></li>
            <li><?php echo $this->tag->linkTo(array("books/create", '<i class="fa fa-angle-double-right"></i>Create Book')); ?></li>
            <li><?php echo $this->tag->linkTo(array("questions", '<i class="fa fa-angle-double-right"></i>Question & Answers')); ?></li>
        </ul>
    </li>
    <li class="">
        <?php echo $this->tag->linkTo(array("users", '<i class="fa fa-users"></i> <span>Users</span>')); ?>
    </li>
</ul>