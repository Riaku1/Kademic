<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'results';

		/* data for selected record, or defaults if none is selected */
		var data = {
			assess: <?php echo json_encode(['id' => $rdata['assess'], 'value' => $rdata['assess'], 'text' => $jdata['assess']]); ?>,
			student_name: <?php echo json_encode(['id' => $rdata['student_name'], 'value' => $rdata['student_name'], 'text' => $jdata['student_name']]); ?>,
			total_marks: <?php echo json_encode($jdata['total_marks']); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for assess */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'assess' && d.id == data.assess.id)
				return { results: [ data.assess ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for assess autofills */
		cache.addCheck(function(u, d) {
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'assess' && d.id == data.assess.id) {
				$j('#total_marks' + d[rnd]).html(data.total_marks);
				return true;
			}

			return false;
		});

		/* saved value for student_name */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'student_name' && d.id == data.student_name.id)
				return { results: [ data.student_name ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

