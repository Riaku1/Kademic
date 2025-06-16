<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'fees_payments';

		/* data for selected record, or defaults if none is selected */
		var data = {
			student: <?php echo json_encode(['id' => $rdata['student'], 'value' => $rdata['student'], 'text' => $jdata['student']]); ?>,
			class: <?php echo json_encode($jdata['class']); ?>,
			term: <?php echo json_encode($jdata['term']); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for student */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'student' && d.id == data.student.id)
				return { results: [ data.student ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for student autofills */
		cache.addCheck(function(u, d) {
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'student' && d.id == data.student.id) {
				$j('#class' + d[rnd]).html(data.class);
				$j('#term' + d[rnd]).html(data.term);
				return true;
			}

			return false;
		});

		cache.start();
	});
</script>

