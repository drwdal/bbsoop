<h2 class="rule">Contents</h2>
<ol>
	<li><a href="#model_methods">Model methods</a></li>
	<li>
		<a href="#functions">Functions</a>
		<ol>
			<li><a href="#routing">Routing</a></li>
			<li><a href="#views">Views</a></li>
			<li><a href="#record_helpers">Record helpers/formatting</a></li>
			<li><a href="#string_helpers">String helpers/formatting</a></li>
			<li><a href="#validation">Validation/processing</a></li>
		</ol>
	</li>
	<li><a href="#users">Users</a></li>
	<li><a href="#core_vars">Core variables and objects</a></li>
	<li><a href="#markup">Text markup</a></li>
</ol>
<h2 class="rule" id="model_methods">Model methods</h2>
<p>Initialize the database connection as <code>$DB</code></p>
<pre>_APPCONFIG::start_app ( );</pre>
<p>Fetch one record as object</p>
<pre>$topic = Topic::find ( ( int ) $ID );</pre>
<p>Fetch many records as an array of objects</p>
<pre>$topics = Topic::find ( array ( 'conditions' => array ( "`status` = 'published'" ), 
	'select' => 'ID, title, created_at, status', 'order' => 'created_at DESC', 'limit' => 400 ) );</pre>
<p>Initialize one new record with values</p>
<pre>$topic = new Topic ( array ( 'title' => $_POST['topic']['title'], 
	'body' => $_POST['topic']['body'], 'user_ID' => $_SESSION['logged_in_user']['ID'] ) );</pre>
<p>Validate a record; returns TRUE or FALSE—sets <code>$topic->_hash</code> if valid</p>
<pre>$topic->validate ( );</pre>
<p>Save a new record (validates and <code>mysqli_real_escape</code> before saving, sets <code>$errors</code> if necessary); returns the new record’s ID or returns FALSE.</p>
<pre>$new_ID = $topic->create ( );</pre>
<p>Save specific database fields for an existing record</p>
<pre>$topic->update ( array ( 'media_ID' ) );</pre>
<p>Load all settings (lazy/inefficient)</p>
<pre>Setting::load ( );</pre>
<p>Load all settings (efficient)</p>
<pre>Setting::load ( array ( 'select' => 'name, type, value' ) );</pre>
<p>Load specific settings</p>
<pre>Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MEDIA' 
	OR category = 'MODERATION'", 'select' => 'category, name, value, type' ) );</pre>
<p>Update a counter</p>
<pre>coming…</pre>

<h2 class="rule" id="functions">Functions</h2>
<h3 id="routing">Routing</h3>
<p>Checks for logged in user, validates their <code>type, status and ban_expires</code> and redirects on failure.</p>
<pre>ensure_login ( $user_type = ADMIN_TYPE, $redirect_to = '' )</pre>
<p>Redirects to a new location within the site; stops all processing when called.</p>
<pre>redirect_to ( $uri = 'home/index', $status = 303 );</pre>
<p>Set an error code for HTTP status</p>
<pre>set_error ( 404 );</pre>
<p>Handles the <code>$error</code> variable from <code>set_error ( );</code> and displays human-readable information.</p>
<pre>render_error ( );</pre>

<h3 id="views">Views</h3>
<p></p>
<pre>IMGBOARD_render_view ( 'path' );</pre>

<h3 id="record_helpers">Record helpers/formatting</h3>
<p>Outputs a topic or reply; calls other functions for each part of the record, depending on environment; creates or retrieves the <code>$record->partial_cache</code> when <code>CACHE_LEVEL >= 1</code></p>
<pre>generate_post ( $record );</pre>
<p>Anonymous <strong>A</strong>, Anonymous <strong>B</strong>, Anonymous <strong>C</strong>, etc and (OP) or (you); cannot be cached :-(</p>
<pre>anonymous_user_mapping ( $user_ID );</pre>

<h3 id="string_helpers">String helpers/formatting</h3>
<p>Takes string and sanitizes, turns markup to HTML, and returns.</p>
<pre>generate_post_body ( $body );</pre>
<p>Takes a bytes integer and returns a human-readable KB/MB/GB string.</p>
<pre>humanize_bytes ( 200000 );</pre>
<p>Echoes a hidden input with the nonce value.</p>
<pre>nonce_for_form ( );</pre>

<h3 id="validation">Validation/processing</h3>
<p>Returns TRUE if HTTP POST and valid nonce</p>
<pre>valid_post ( );</pre>
<p>Returns TRUE if the nonce validates, sets <code>$errors</code> and returns FALSE if not.</p>
<pre>nonce_is_valid ( );</pre>

<h2 class="rule" id="users">Users</h2>
<p>Types as integers</p>
<pre>
define ( 'ADMIN_TYPE', 5 );
define ( 'MODERATOR_TYPE', 4 );
define ( 'REGULAR_TYPE', 1 );
define ( 'PUBLIC_TYPE', 0 );
</pre>

<h2 class="rule" id="core_vars">Core variables and objects</h2>
<p><code>$controller</code>, <code>$action</code>, <code>$ID</code>, <code>$fragment</code>, and <code>$extra</code> represent <code>/all/the/successive/URI/parts</code>, respectively. All are strings.</p>
<p><code>$DB</code> is a mysqli object, but queries should preferrably go through the models</p>
<p><code>$page_title</code> is the page <code>&lt;h1&gt;</code>—content must be sanitized with <code>htmlspecialchars ( );</code></p>
<p><code>$site_title</code> is the site <code>&lt;h2&gt;</code>—content must be sanitized with <code>htmlspecialchars ( );</code></p>
<p><code>$_SESSION['notice']</code> contains one yellow-highlighted message; it is displayed and cleared on the next page load.</p>
<p><code>$errors</code> contains an array of human-readable error messages as strings; they are meant to display on that page load.</p>
<p><code>BASE_URI</code>, <code>BASE_PATH</code>, <code>DOMAIN</code>, <code>TEMPLATE_PATH</code></p>
<p><code>IMGBOARD_INIT == 1</code> if the request has come through <code>index.php</code></p>

<h2 class="rule" id="markup">Text markup</h2>
<p>'''<strong>Bold</strong>'''</p>
<p>''<em>Italic</em>''</p>
<p>**<span class="spoiler">spoiler</span>**</p>
<p>%%<span class="highlight">highlight</span>%%</p>
<p>++<del>strikeout</del>++</p>
<p><span class="quote">&gt; quote</span></p>
<blockquote><p>[[blockquote]]</p></blockquote>





