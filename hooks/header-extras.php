<script src="hooks/AppGiniHelper.min.js"></script>

<script>
// this is for centering the login page
	$j(function(){
		if($j("#login_splash").length){
			$j("#login_splash").remove();
			$j(".container> .row> .col-sm-6").addClass("col-sm-offset-3");
			$j(".container> .row> .col-lg-4").addClass("col-lg-offset-4");

		}
	});
</script>

