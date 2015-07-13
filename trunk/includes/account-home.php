<!-- START account-home.php -->
<?php

$ds = new Geodigs_Document_Store();
$cm = new Geodigs_Calendars();

switch ($_GET['action']) {
	case 'upload':
		if (count($_FILES) > 0) {
			$ds->add_file($_POST['fileName'], $_POST['description'], 'active');
		}
		break;
	case 'download':
		$ds->download_file($_GET['id']);
		break;
	case 'edit':
		if ($_POST['id']) {
			$ds->update_file($_POST['id'], $_POST['fileName'], $_POST['description'], 'active');
		}
		break;
	case 'delete':
		$ds->delete_file($_GET['id']);
		break;
}

$files = $ds->get_files($_SESSION['gd_user']->id);
$calendars = $cm->get_calendars_for_user($_SESSION['gd_user']->id);
?>

<div id="gd" class="gd-my-account">
	<?php if ($files): ?>
		<h1 class="text-primary"><strong>Document Store</strong></h1>
		<table>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Uploaded</th>
			</tr>
			<?php foreach ($files as $file): ?>
				<tr>
					<td><a href="?action=download&id=<?=$file->id?>"><?=$file->name?></a></td>
					<td><?=$file->description?></td>
					<td><?=date('F j, Y, g:i a', strtotime($file->modDate))?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else: ?>
		<h1 class="text-primary"><strong>No Files Found</strong></h1>
	<?php endif; ?>
	<h1 class="text-primary"><strong>Calendar</strong></h1>
	<?php foreach ($calendars as $calendar): ?>
		<iframe class="gd-calendar" src="<?=str_replace(array('http://', 'https://'), PROTOCOL, $calendar->link)?>" frameborder="0"></iframe>
	<?php endforeach; ?>
</div>
<!-- END account-home.php -->