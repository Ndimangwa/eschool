<!--Begin of Cross Reference List Insertion-->
<?php 
	if (! is_null($criteria1->getExclusionCriteriaList()) && sizeof($criteria1->getExclusionCriteriaList()) > 0)	{
		$objectList1 = $criteria1->getExclusionCriteriaList();
?>
			<table class="pure-table pure-table-aligned ui-sys-table-search-results ui-sys-table-search-results-2">
				<thead>
					<tr>
						<th colspan="3">Exclusion Criteria List</th>
					</tr>
					<tr>
						<th></th>
						<th>Level Name</th>
						<th>Level Code</th>
					</tr>
				</thead>
<?php 
	$sqlReturnedRowCount = 0;
	$perTbodyCounter = 0;
	$numberOfTBodies = 0;
	$maximumRowsPerPage = intval($profile1->getMaximumNumberOfDisplayedRowsPerPage());
	$tbodyClosed = true;
	foreach ($objectList1 as $obj1)	{
		if (is_null($obj1)) continue;
		if ($tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "<tbody tbody-index=\"$numberOfTBodies\">"; 
			$numberOfTBodies++;
			$tbodyClosed = false;
		} else if (! $tbodyClosed && ($sqlReturnedRowCount % $maximumRowsPerPage) == 0)	{
			echo "</tbody><tbody tbody-index=\"$numberOfTBodies\" class=\"ui-sys-hidden\">";
			$numberOfTBodies++;
			$perTbodyCounter = 0;
		}
		//In All Cases we need to display tr
		$pureTableOddClass = "";
		if (($perTbodyCounter % 2) != 0) $pureTableOddClass = "pure-table-odd";
?>
		<tr class="<?= $pureTableOddClass ?>">
			<td><?= $sqlReturnedRowCount + 1 ?></td>
			<td><?= $obj1->getLevelName() ?></td>
			<td><?= $obj1->getLevelCode() ?></td>
		</tr>
<?php
		$perTbodyCounter++;
		$sqlReturnedRowCount++;
	}
	if (! $tbodyClosed)	{
		echo "</tbody>";
 		$tbodyClosed = true;
	}
?>
			</table>
			<ul class="ui-sys-pagination-2"></ul>
<script type="text/javascript">
(function($)	{
	$(function()	{
		 $('ul.ui-sys-pagination-2').twbsPagination({
			totalPages: <?= $numberOfTBodies ?>,
			visiblePages: 5,
			onPageClick: function (event, page) {
				//page is page number 
				var $tbodyList1 = $('table.ui-sys-table-search-results-2 tbody');
				if (! $tbodyList1.length) return;
				//Hide All tbody 
				$tbodyList1.addClass('ui-sys-hidden');
				//Now show only the one corresponding to this page
				$tbodyList1.eq(page-1).removeClass('ui-sys-hidden');			
			}
		});
	});
})(jQuery);
</script>

<?php
	}
?>
			<!--End of Cross Reference List Insertion-->