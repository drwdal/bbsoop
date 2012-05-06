<p>TODO: two modes… replace or match</p>
<p>
	Replace should be plain strings and cannot destructively parse the text first (i.e. line breaks or hyphens); it works best when cleaning up poor type (dont, wont, i, etc)<br />
	(^|[\W])nigger|dyke(s?)([\W]|$)
</p>
<p>
	Match can be useful for spam detection and can use destructive parsing (remove whitespace, hypens, etc); it works best to discourage certain subjects (boxxy, tinychat.com, Lockerz, etc)<br />
	(^|[\W])nigger|dyke(s?)([\W]|$)
</p>
<h2 class="rule"><a href="javascript:void(0);" onclick="jQuery('#new-word-filter').slideToggle('fast');">Add new…</a></h2>
<form action="" method="post" class="wrapper c" id="new-word-filter" style="display: none;">
	<p><label class="small" for="wordfilter_action">Action</label> replace, reject, CAPTCHA, ban</p>
	<p></p>
</form>
<h2 class="rule"><a href="javascript:void(0);" onclick="jQuery('#word-filter-test').slideToggle('fast');">Filter test…</a></h2>
<form action="" method="post" id="word-filter-test" class="wrapper c">
	<p class="medium"><label class="medium">Mode(s)</label></p>
	<div class="radio-group wrapper">
		<p><input type="radio" id="wordfilter_mode_match" name="wordfilter[mode]" value="match" onclick="jQuery('.wordfilter-match').css('display','block');jQuery('.wordfilter-replace').css('display','none');" /> <label for="wordfilter_mode_match">match</label>  <span class="help">destructively analyzes text to find matches (e.g. spam, banned words)</span></p>
		<p><input type="radio" id="wordfilter_mode_replace" name="wordfilter[mode]" value="replace" onclick="jQuery('.wordfilter-replace').css('display','block');jQuery('.wordfilter-match').css('display','none');" /> <label for="wordfilter_mode_replace">replace</label> <span class="help">non-destructive replacements (e.g. cleans up typos, makes substitutions)</span></p>
		<p><input type="hidden" name="wordfilter[case_sensitive]" value="0" /><input type="checkbox" id="wordfilter_case_sensitive" name="wordfilter[case_sensitive]" value="1" /> <label for="wordfilter_case_sensitive">case-sensitive</label></p>
	</div>
	<hr />
	<p><label class="medium" for="wordfilter_text">Sample text</label> <textarea class="xlarge" id="wordfilter_text" name="wordfilter[text]" cols="60" rows="6"></textarea></p>
	<hr />
	<p><label class="medium" for="wordfilter_pattern">Pattern</label> <input type="text" class="xlarge" id="wordfilter_pattern" name="wordfilter[pattern]" /> <span class="help">Regular expression</span></p>
	<p class="wordfilter-replace" style="display: none;"><label class="medium" for="wordfilter_replacement">Replacement</label> <input type="text" class="xlarge" id="wordfilter_replacement" name="wordfilter[replacement]" /></p>
	<p class="wordfilter-match" style="display: none;"><label class="medium" for="wordfilter_replacement">User message</label> <input type="text" class="xlarge" id="wordfilter_message" name="wordfilter[message]" /></p>
	<div class="wordfilter-match wrapper c" style="display: none;">
		<hr />
		<p><label class="medium" for="wordfilter_mode">Method</label> <select id="wordfilter_mode" name="wordfilter[method]" class="large"><option value="default">strip new lines (always on)</option><option value="whitespace">…and strip whitespace</option><option value="ascii">…and convert to ascii</option></select></p>
		<p><label class="medium" for="wordfilter_action">Action</label> <select id="wordfilter_action" name="wordfilter[action]" class="large"><option value="default">post failure</option><option value="CAPTCHA">…and show CAPTCHA</option><option value="ban">automatic ban</option></select></p>
	</div>
	<p><label class="medium">&nbsp;</label> <input type="button" value="Test" onclick="wordfilter_test(); return false" /> <input type="button" value="Benchmark" onclick="wordfilter_test('benchmark'); return false" /></p>
	<hr />
	<p><label class="medium">Applied pattern</label> <input type="text" class="xlarge" id="wordfilter_applied_pattern" readonly="readonly" /></p>
	<p><label class="medium">Result</label> <textarea class="xlarge" id="wordfilter_result" readonly="readonly" cols="60" rows="6"></textarea></p>
	<p><label class="medium">Benchmark</label> <textarea class="large" id="wordfilter_benchmark" readonly="readonly" cols="30" rows="6"></textarea> <span class="help">seconds to run <span id="benchmark_loops">1</span> test</span></p>
</form>
