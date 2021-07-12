<?php
/**
 * @package         Joomla.Administrator
 * @subpackage      com_cronjobs
 *
 * @copyright   (C) 2021 Open Source Matters, Inc. <https://www.joomla.org>
 * @license         GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

HTMLHelper::_('behavior.multiselect');

try
{
	$app = Factory::getApplication();
}
catch (Exception $e)
{
	die('Failed to get app');
}

$user = $app->getIdentity();
$userId = $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.lft' && strtolower($listDirn) == 'asc');
$section = null;
$mode = false;


if ($saveOrder && !empty($this->items))
{
	// TODO : Check if this works
	$saveOrderingUrl = 'index.php?option=com_cronjobs&task=cronjobs.saveOrderAjax&' . Session::getFormToken() . '=1';
	HTMLHelper::_('draggablelist.draggable');
}
?>

<form action="<?php echo Route::_('index.php?option=com_cronjobs&view=cronjobs'); ?>" method="post" name="adminForm"
	  id="adminForm">
	<div id="j-main-container" class="j-main-container">
		<?php
		// Search tools bar
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>

		<!-- If no cronjobs -->
		<?php if (empty($this->items))
:
			?>
			<!-- No cronjobs -->
			<div class="alert alert-info">
				<span class="icon-info-circle" aria-hidden="true"></span><span
						class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php endif; ?>

		<!-- If there are cronjobs, we start with the table -->
		<?php if (!empty($this->items))
:
			?>
			<!-- Cronjobs table starts here -->
			<table class="table" id="categoryList">

				<caption class="visually-hidden">
					<?php echo Text::_('COM_CRONJOBS_TABLE_CAPTION'); ?>,
					<span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
					<span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
				</caption>

				<!-- Cronjobs table header -->
				<thead>
				<tr>
					<!-- Select all -->
					<td class="w-1 text-center">
						<?php echo HTMLHelper::_('grid.checkall'); // "Select all" checkbox ?>
					</td>
					<!-- Ordering?-->
					<th scope="col" class="w-1 d-none d-md-table-cell text-center">
						<!-- Might need to adjust method args here -->
						<?php echo HTMLHelper::_('searchtools.sort', '', 'a.job_id', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
					</th>
					<!-- Status -->
					<th scope="col" class="w-1 text-center">
						<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.enabled', $listDirn, $listOrder); ?>
					</th>
					<!-- Job name-->
					<th scope="col">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<!-- Job Type -->
					<th scope="col">
						<?php echo HTMLHelper::_('searchtools.sort', 'JOB_TYPE', 'a.type', $listDirn, $listOrder) ?>
					</th>
					<!-- Job access-->
					<th scope="col" class="w-10 d-none d-md-table-cell">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
					</th>
					<!-- Job ID -->
					<th scope="col" class="w-5 d-none d-md-table-cell">
						<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.job_id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>

				<!-- Table body begins -->
				<tbody <?php if ($saveOrder)
	:
					?>
					class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="true"<?php
					   endif; ?>>
				<?php
				foreach ($this->items as $i => $item)
	:
					// TODO : Check if $user->authorise() calls work as they should
					$orderKey = $item->id;
					$canCreate = $user->authorise('core.create', 'com_cronjobs');
					$canEdit = $user->authorise('core.edit', 'com_cronjobs');
					$canChange = $user->authorise('core.edit.state', 'com_cronjobs'); ?>

					<!-- Row begins -->
					<tr class="row<?php echo $i % 2; ?>"
						data-item-id="<?php echo $item->id; ?>"
						data-draggable-group="<?php echo $item->id; ?>">
						<!-- Item Checkbox -->
						<td class="text-center">
											<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
						</td>
						<!-- Draggable handle -->
						<td class="text-center d-none d-md-table-cell">
											<?php
											$iconClass = '';

											if (!$canChange)
		{
												$iconClass = ' inactive';
											}
											elseif (!$saveOrder)
		{
															$iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
											}
											?>
							<span class="sortable-handler<?php echo $iconClass ?>">
									<span class="icon-ellipsis-v"></span>
								</span>
							<?php if ($canChange && $saveOrder)
		:
								?>
								<input type="text" class="hidden" name="order[]" size="5"
									   value="<?php echo $orderKey + 1; ?>">
							<?php endif; ?>
						</td>
						<!-- Enabled status -->
						<td class="text-center">
							<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'cronjobs.', $canChange); ?>
						</td>
						<!-- Item name, edit link, and note (TODO: should it be moved?) -->
						<th scope="row">
							<?php
							if ($canEdit)
		:
								?>
								<a href="<?php echo Route::_('index.php?option=com_cronjobs&task=cronjob.edit&id=' . $item->id); ?>"
								   title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
									<?php echo $this->escape($item->title); ?></a>
							<?php else

		:
								?>
								<?php echo $this->escape($item->title); ?>
							<?php endif; ?>

							<?php
							if (empty($item->note))
		:
								?>
								<!-- TODO: Remove or modify 'note' section -->
								<?php echo 'No note :)'; ?>
							<?php else

		:
								?>
								<?php echo Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', 'Alias', $this->escape($item->note)); ?>
							<?php endif; ?>
						</th>
						<!-- Item type -->
						<td class="small d-none d-md-table-cell">
							<?php echo $this->escape($item->type); ?>
						</td>
						<!-- TODO: What should be done about Multilang? Is it needed here? -->
						<?php if (Multilanguage::isEnabled())
		:
							?>
							<td class="small d-none d-md-table-cell">
								<?php echo LayoutHelper::render('joomla.content.language', $item); ?>
							</td>
						<?php endif; ?>
						<!-- Cronjob access -->
						<!-- TODO: Implement access in cronjobs and have a look here -->
						<td class="small d-none d-md-table-cell text-center">
							<span class="badge bg-info">
								<?php echo 'Unknown'; ?>
							</span>
						</td>
						<td class="d-none d-md-table-cell">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<?php // Load the pagination. ?>
			<?php echo $this->pagination->getListFooter(); ?>

			<?php // Load the batch processing form if user is allowed ?>
			<?php if ($user->authorise('core.create', 'com_cronjobs')
			&& $user->authorise('core.edit', 'com_cronjobs')
			&& $user->authorise('core.edit.state', 'com_cronjobs'))
	:
	?>
				<?php echo HTMLHelper::_(
					'bootstrap.renderModal',
					'collapseModal',
					array(
							'title' => Text::_('com_cronjobs_BATCH_OPTIONS'),
							'footer' => $this->loadTemplate('batch_footer'),
					),
					$this->loadTemplate('batch_body')
				); ?>
			<?php endif; ?>
		<?php endif; ?>

		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="0">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>