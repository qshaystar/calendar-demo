<?php include('header.php') ?>
<?php include('data.php') ?>
<?php include('read.php') ?>
<?php include('template.php') ?> 
 
<div id="calender" data-year="<?= date('Y')?>" data-month="<?= date('m')?>">
	<div id="header">
		
		<?= date('Y')?>/<?= date('M')?>
	</div>

	<div id="days" class="clearfix">
		<div class="day">SUN</div>
		<div class="day">MON</div>
		<div class="day">TUE</div>
		<div class="day">WED</div>
		<div class="day">THR</div>
		<div class="day">FRI</div>
		<div class="day">SAT</div>
	</div>

	<div id="dates" class="clearfix">
		<?php foreach ($dates as $key => $date): ?>
			 <div class="date-block <?= (is_null($date))? 'empty':'' ?> "  data-date="<?= $date?>">   
			 	<!--判斷如果為null加empty class 記得裡面填值而非整個陣列-->
				<div class="date"><?= $date ?></div>
				<div class="events">
				</div>
			</div>
		<?php endforeach ?>		
	</div>
</div>

<div id="info-panel">
	<div class="close">x</div>
	<!-- 使用form比較好一次收集event的資料，否則要一個個挖 -->
	<form>   
		<input type="hidden" name="id">
		<div class="title">
			<label>event</label> <br>
			<input type="text" name="title">
		</div>
		<div class="error-msg">
			<div class="alert alert-danger">error</div>
		</div>

		<div class="time-picker">
			<div class="selected-date">
				<span class="month"></span>/<span class="date"></span>
				<input type="hidden" name="year">
				<input type="hidden" name="month">
				<input type="hidden" name="date">
			</div>
			
			<div class="from">
				<label for="from">from</label> <br>
				<input id="from" type="time" name="start_time">
			</div>
			<div class="to">
				<label for="to">to</label> <br>
				<input id="to" type="time" name="end_time">
			</div>
		</div>

		<div class="description">
			<label>description</label> <br>
			<textarea name="description" id="description" name="description"></textarea>
			
		</div>	

	</form>


	<div class="buttons clearfix">
		<button class="create">create</button>
		<button class="update">update</button>
		<button class="cancel">cancel</button>
		<button class="delete">delete</button>
		<!-- create: create / cancel -->
		<!-- update: update / cancel / delete -->
	</div>
</div>



<?php include('footer.php') ?>