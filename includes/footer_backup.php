
    <!--copyright starts here-->
    <div class=" copyright">
    	<div class="copyright_left">
      Copyright &copy; 2010 Bail Bonds Bail Man  All Right Reserved.<br />
      <a href="http://www.ifindable.com/" target="_blank" class="copyright_right_link">Website Design</a>   |   <a href="http://www.ifindable.com/search_engine_optimization.htm" target="_blank" class="copyright_right_link">SEO</a> | <a href="http://www.ifindable.com/" class="copyright_right_link">iFindable.com</a></div>
      <div class="copyright_right">
      <a href="<?php echo $CFG->siteroot."/" ; ?>index.html" class="copyright_right_link">Home</a>   |   <a href="<?php echo $CFG->siteroot."/" ; ?>why_paul_columbis.html" class="copyright_right_link">Why Paul Columbis</a>     |   <a href="<?php echo $CFG->siteroot."/" ; ?>jail_location.html" class="copyright_right_link">Jail Locations</a>   |   <a href="<?php echo $CFG->siteroot."/" ; ?>court_locations.html" class="copyright_right_link">Court Locations</a>   |<a href="<?php echo $CFG->siteroot."/" ; ?>inmate_status.html" class="copyright_right_link">   Inmate Status</a> | <a href="<?php echo $CFG->siteroot."/" ; ?>testimonials.html" class="copyright_right_link">Testimonials</a>   |   <a href="<?php echo $CFG->siteroot."/" ; ?>contact_us.html" class="copyright_right_link">Contact Me</a>   |    <a href="<?php echo $CFG->siteroot."/" ; ?>sitemap.html" class="copyright_right_link">Sitemap</a> </div>
    </div>
    <!--copyright close here-->
    
</div>
<!--main content close here-->

</div>
<!--wrapper close here-->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17587358-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php
if($page_name == "index")
{
		$sql = "SELECT * FROM {$CFG->prefix}testimonial WHERE status = 'active' ORDER BY added_date"; 
		$featured_test = get_records_sql($sql);

		$test_text = '';
		$delay =  0;
		$delay_time = 6000;


		for($j=0;$j<50;$j++)
			{
				foreach($featured_test as $test)
				{
					$comment_wrap = addslashes(wrap_string($test->comment,250));
					$test_text .= <<<OP

					(function() {
						this.managerOne.show({
							title: '',
							user_name: '$test->contact_person',
							user_site_name: '$test->company_name',
							message: ''
						});
					}).delay($delay,this);

OP;
					$delay = $delay + $delay_time;
				}
			}
		

		echo <<<OP
		<script type="text/javascript">
			window.addEvent('domready', function(){
	
			if (!this.ManagerOne)
				this.managerOne = new Notimoo({parent:'testimonial',visibleTime:'8000',width:'200'});
					$test_text
		});

		</script>
OP;
	
}
?>
</body>
</html>