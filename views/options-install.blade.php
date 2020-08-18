<div class="form-group  mb-1 d-block">
	<label class="mr-1">
		MaxMind GeoLite2 Key &ndash; <a href="https://www.maxmind.com/en/geolite2/signup">sign-up</a>
	</label>
	<input type="text" class="form-control" name="maxmind"
	       value="{{ \Preferences::get(\Module\Support\Webapps\App\Type\Discourse\Handler::MAXMIND_KEY_PREF) }}"/>
</div>