<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'rizalts';

		/* data for selected record, or defaults if none is selected */
		var data = {
			exm_id: <?php echo json_encode(['id' => $rdata['exm_id'], 'value' => $rdata['exm_id'], 'text' => $jdata['exm_id']]); ?>,
			student_name: <?php echo json_encode(['id' => $rdata['student_name'], 'value' => $rdata['student_name'], 'text' => $jdata['student_name']]); ?>,
			total_marks: <?php echo json_encode($jdata['total_marks']); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for exm_id */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'exm_id' && d.id == data.exm_id.id)
				return { results: [ data.exm_id ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for exm_id autofills */
		cache.addCheck(function(u, d) {
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'exm_id' && d.id == data.exm_id.id) {
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

