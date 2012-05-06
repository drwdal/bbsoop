				<ul id="nav" class="c">
					<li class="<?php if ( $action == 'intro' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/intro">Admin</a></li>
					<li class="<?php if ( $action == 'app_settings' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/app_settings">App Settings</a></li>
					<li class="<?php if ( $action == 'categories' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/categories">Categories</a></li>
					<li class="<?php if ( $action == 'moderation' || $action == 'media' || $action == 'replies' || $action == 'topics' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/moderation">Moderation</a></li>
					<li class="<?php if ( $action == 'pages' || $action == 'page_edit' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/pages">Pages</a></li>
					<li class="<?php if ( $action == 'users' || $action == 'user' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/users">Users</a></li>
					<li class="<?php if ( $action == 'remote_addresses' || $action == 'remote_address' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/remote_addresses">Remote addresses</a></li>
					<li class="<?php if ( $action == 'statistics' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/statistics">Statistics</a></li>
					<li class="<?php if ( $action == 'wordfilters' ) { echo "current"; } ?>"><a href="<?php echo BASE_URI; ?>admin/wordfilters">Word filters</a></li>
					<li class="float-right"><a href="<?php echo BASE_URI; ?>account/logout" class="logout">Log out</a></li>
					<li class="float-right"><a href="<?php echo BASE_URI; ?>">Home</a></li>
				</ul>
