<div id="edit-users-header" class="page-header">
    <h1>编辑用户</h1>
</div> <!-- /edit-users-header -->
<div id="edit-users-section" class="section">
    <?php
        if ( isset($available_grades) && count($available_grades) != 0 ) {
            require_once(APPPATH.'views/admin/editusers-content.php');
        } else {
            echo '<div class="alert alert-error"><strong>温馨提示: </strong>暂无可用数据.</div>';
        }
    ?>
</div> <!-- /edit-users-header -->