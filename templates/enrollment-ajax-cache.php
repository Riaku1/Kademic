<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'enrollment';

		/* data for selected record, or defaults if none is selected */
		var data = {
			full_name: <?php echo json_encode(['id' => $rdata['full_name'], 'value' => $rdata['full_name'], 'text' => $jdata['full_name']]); ?>,
			class: <?php echo json_encode(['id' => $rdata['class'], 'value' => $rdata['class'], 'text' => $jdata['class']]); ?>,
			year: <?php echo json_encode($jdata['year']); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for full_name */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'full_name' && d.id == data.full_name.id)
				return { results: [ data.full_name ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for class */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'class' && d.id == data.class.id)
				return { results: [ data.class ], more: false, elapsed: 0.01 };
			return false;
		});

		/* saved value for class autofills */
		cache.addCheck(function(u, d) {
			if(u != tn + '_autofill.php') return false;

			for(var rnd in d) if(rnd.match(/^rnd/)) break;

			if(d.mfk == 'class' && d.id == data.class.id) {
				$j('#year' + d[rnd]).html(data.year);
				return true;
			}

			return false;
		});

		cache.start();
	});
</script>

