<?php
// Require object
if (empty($ics_data)) { return false; }

global $R34ICS;
global $wp_locale;

$days_of_week = $R34ICS->get_days_of_week();
$start_of_week = get_option('start_of_week', 0);

$today = date_i18n('Ymd', current_time('timestamp'));

$ics_calendar_classes = array(
	'ics-calendar',
	(!empty($args['hidetimes']) ? ' hide_times' : ''),
	(!empty($args['toggle']) ? ' toggle' : ''),
);
?>

<section class="<?php echo esc_attr(implode(' ', $ics_calendar_classes)); ?>">

	<?php
	// Title and description
	if (!empty($ics_data['title'])) {
		?>
		<h2 class="ics-calendar-title"><?php echo $ics_data['title']; ?></h2>
		<?php
	}
	if (!empty($ics_data['description'])) {
		?>
		<p class="ics-calendar-description"><?php echo $ics_data['description']; ?></p>
		<?php
	}
	
	// Empty calendar message
	if (empty($ics_data['events'])) {
		?>
		<p class="ics-calendar-error"><?php _e('No events found.', 'R35ICS'); ?></p>
		<?php
	}
	
	// Display calendar
	else {
		?>
	
		<select class="ics-calendar-select" style="display: none;">
			<?php
			// Build list from earliest to latest month
			foreach (array_keys((array)$ics_data['events']) as $year) {
				for ($m = 1; $m <= 12; $m++) {
					$month = $m < 10 ? '0' . $m : '' . $m;
					$ym = $year . $month;
					if ($ym < $ics_data['earliest']) { continue; }
					if ($ym > $ics_data['latest']) { break(2); }
					?>
					<option value="<?php echo date_i18n('Ym', mktime(0,0,0,$month,1,$year)); ?>"><?php echo ucwords(date_i18n('F Y', mktime(0,0,0,$month,1,$year))); ?></option>
					<?php
				}
			}
			?>
		</select>
		
		<?php
		// Build monthly calendars
		foreach (array_keys((array)$ics_data['events']) as $year) {
			for ($m = 1; $m <= 12; $m++) {
				$month = $m < 10 ? '0' . $m : '' . $m;
				$ym = $year . $month;
				if ($ym < $ics_data['earliest']) { continue; }
				if ($ym > $ics_data['latest']) { break(2); }
				$first_date = mktime(0,0,0,$month,1,$year);
				$month_label = ucwords(date_i18n('F Y', $first_date));
				
				// Build month's calendar
				?>
				<div class="ics-calendar-month-wrapper" data-year-month="<?php echo date_i18n('Ym', mktime(0,0,0,$month,1,$year)); ?>" style="display: none;">
	
					<h3 class="ics-calendar-label"><?php echo $month_label; ?></h3>

					<table class="ics-calendar-month-grid">
						<thead>
							<tr>
								<?php
								foreach ((array)$days_of_week as $w => $dow) {
									?>
									<th data-dow="<?php echo $w; ?>"><?php echo $dow; ?></th>
									<?php
								}
								?>
							</tr>
						</thead>

						<tbody>
							<tr>
								<?php
								$first_dow = $this->first_dow($first_date);
								if ($first_dow < $start_of_week) { $first_dow = $first_dow + 7; }
								for ($off_dow = $start_of_week; $off_dow < $first_dow; $off_dow++) {
									?>
									<td class="off" data-dow="<?php echo intval($off_dow); ?>"></td>
									<?php
								}
								for ($day = 1; $day <= date_i18n('t',$first_date); $day++) {
									$date = mktime(0,0,0,date_i18n('n',$first_date),$day,date_i18n('Y',$first_date));
									$dow = date_i18n('w',$date);
									$day_events = isset($ics_data['events'][$year][$month][date_i18n('d',$date)]) ? $ics_data['events'][$year][$month][date_i18n('d',$date)] : null;
									$comp_date = date_i18n('Ymd', $date);
									if ($dow == $start_of_week) {
										?>
										</tr><tr>
										<?php
									}
									?>
									<td data-dow="<?php echo intval($dow); ?>" class="<?php
									if ($comp_date < $today) {
										echo 'past';
									}
									elseif ($comp_date == $today) {
										echo 'today';
									}
									else {
										echo 'future';
									}
									if (count((array)$day_events) == 0) {
										echo ' empty';
									}
									?>">
										<div class="day">
											<span class="phone_only"><?php echo date_i18n('l', $date); ?></span>
											<?php echo $day; ?>
										</div>
										<ul class="events">
											<?php
											foreach ((array)$day_events as $time => $events) {
												$all_day_indicator_shown = false;
												foreach ((array)$events as $event) {
													if ($time == 'all-day') {
														?>
														<li class="event all-day">
															<?php
															if (!$all_day_indicator_shown) {
																?>
																<span class="all-day-indicator"><?php _e('All Day', 'R35ICS'); ?></span>
																<?php
																$all_day_indicator_shown = true;
															}
															?>
															<span class="title<?php
															if ((!empty($args['eventdesc']) && !empty($event['eventdesc'])) || (!empty($args['location']) && !empty($event['location']))) {
																echo ' has_desc" title="';
																if (!empty($args['eventdesc']) || !empty($event['eventdesc'])) {
																	echo esc_attr($event['eventdesc']) . "\n";
																}
																if (!empty($args['location']) || !empty($event['location'])) {
																	echo esc_attr($event['location']) . "\n";
																}
															}
															?>"><?php echo str_replace('/', '/<wbr />',$event['label']); ?></span>
															<?php
															if (!empty($event['sublabel'])) {
																?>
																<span class="sublabel"><?php echo str_replace('/', '/<wbr />',$event['sublabel']); ?></span>
																<?php
															}
															if (!empty($args['eventdesc']) && !empty($event['eventdesc'])) {
																$eventdesc_class = array(
																	'eventdesc',
																	(empty($args['toggle']) ? 'phone_only' : ''),
																);
																if (intval($args['eventdesc']) > 1) {
																	?>
																	<div class="<?php echo esc_attr(implode(' ', $eventdesc_class)); ?>" title="<?php echo esc_attr($event['eventdesc']); ?>"><?php echo make_clickable(nl2br(wp_trim_words($event['eventdesc'], intval($args['eventdesc'])))); ?></div>
																	<?php
																}
																else {
																	?>
																	<div class="<?php echo esc_attr(implode(' ', $eventdesc_class)); ?>"><?php echo make_clickable(nl2br($event['eventdesc'])); ?></div>
																	<?php
																}
															}
															if (!empty($args['location']) && !empty($event['location'])) {
																$location_class = array(
																	'location',
																	(empty($args['toggle']) ? 'phone_only' : ''),
																);
																?>
																<div class="<?php echo esc_attr(implode(' ', $location_class)); ?>"><?php echo make_clickable(nl2br($event['location'])); ?></div>
																<?php
															}
															?>
														</li>
														<?php
													}
													else {
														?>
														<li class="event">
															<?php
															if (!empty($event['start'])) {
																?>
																<span class="time"><?php
																echo $event['start'];
																if (!empty($event['end']) && $event['end'] != $event['start']) {
																	if (empty($args['showendtimes'])) {
																		?>
																		<span class="show_on_hover">&#8211; <?php echo $event['end']; ?></span>
																		<?php
																	}
																	else {
																		?>
																		&#8211; <?php echo $event['end']; ?>
																		<?php
																	}
																}
																?></span>
																<?php
															}
															?>
															<span class="title<?php
															if ((!empty($args['eventdesc']) && !empty($event['eventdesc'])) || (!empty($args['location']) && !empty($event['location']))) {
																echo ' has_desc" title="';
																if (!empty($args['eventdesc']) || !empty($event['eventdesc'])) {
																	echo esc_attr($event['eventdesc']) . "\n";
																}
																if (!empty($args['location']) || !empty($event['location'])) {
																	echo esc_attr($event['location']) . "\n";
																}
															}
															?>"><?php echo str_replace('/', '/<wbr />',$event['label']); ?></span>
															<?php
															if (!empty($event['sublabel'])) {
																?>
																<span class="sublabel"><?php
																if (empty($event['start']) && !empty($event['end'])) {
																	?>
																	<span class="carryover">&#10554;</span>
																	<?php
																}
																echo str_replace('/', '/<wbr />',$event['sublabel']);
																?></span>
																<?php
															}
															if (!empty($args['eventdesc']) && !empty($event['eventdesc'])) {
																$eventdesc_class = array(
																	'eventdesc',
																	(empty($args['toggle']) ? 'phone_only' : ''),
																);
																if (intval($args['eventdesc']) > 1) {
																	?>
																	<div class="<?php echo esc_attr(implode(' ', $eventdesc_class)); ?>" title="<?php echo esc_attr($event['eventdesc']); ?>"><?php echo make_clickable(nl2br(wp_trim_words($event['eventdesc'], intval($args['eventdesc'])))); ?></div>
																	<?php
																}
																else {
																	?>
																	<div class="<?php echo esc_attr(implode(' ', $eventdesc_class)); ?>"><?php echo make_clickable(nl2br($event['eventdesc'])); ?></div>
																	<?php
																}
															}
															if (!empty($args['location']) && !empty($event['location'])) {
																$location_class = array(
																	'location',
																	(empty($args['toggle']) ? 'phone_only' : ''),
																);
																?>
																<div class="<?php echo esc_attr(implode(' ', $location_class)); ?>"><?php echo make_clickable(nl2br($event['location'])); ?></div>
																<?php
															}
															?>
														</li>
														<?php
													}
												}
											}
											?>
										</ul>
									</td>
									<?php
								}
								for ($off_dow = $dow + 1; $off_dow < ($start_of_week + 7); $off_dow++) {
									?>
									<td class="off" data-dow="<?php echo intval($off_dow); ?>"></td>
									<?php
								}
								?>
							</tr>
						</tbody>
					</table>
	
				</div>
				<?php
			}
		}
	}
	?>

</section>