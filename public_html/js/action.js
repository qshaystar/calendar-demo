$(document).ready(function(){

	var source = $('#event-template').html(); //抓出要新增的template的模板		
	var eventTemplate = Handlebars.compile(source); //將模板用handlebar compile
	
	$.each(events, function(index, event){
		var eventUI = eventTemplate(event);

		var date = event['date'];
		$('#calender').find('.date-block[data-date="'+date+'"]').find('.events').append(eventUI);

	});

	var panel = {
		el: '#info-panel',  //element
		selectedDateBlock: null,
		selectedEvent: null,
		init: function(isNew, e){

			panel.clear();

			//輸入時間 記得傳e才知滑鼠點哪
			panel.updateDate(e); 
			//判斷是create還是update
			if(isNew) {				
				$(panel.el).addClass('new').removeClass('update');
				panel.selectedDateBlock = $(e.currentTarget);
			}    
			else {				
				$(panel.el).addClass('update').removeClass('new');
				panel.selectedDateBlock = $(e.currentTarget).closest('.date-block');
			}
			

		},

		clear: function(){
			//clear data
			$(panel.el).find('input').val(""); //把裡面的值改成空字串
			$(panel.el).find('textarea').val("");
		},

		open: function(isNew, e){

			panel.init(isNew, e);  //記得裡面要傳參數進去！！！

			panel.hideError();
			
			$(panel.el).addClass('open').css({
				top: e.pageY + 'px',
				left: e.pageX + 'px',
			}).find('.title [name="title"]').focus();
		},

		close: function(){
			$(panel.el).removeClass('open');
		},

		updateDate: function(e){
			//判斷滑鼠點擊的地方 找到data-date 注意要找對節點
			if($(e.currentTarget).is('.date-block')) 
				var date = $(e.currentTarget).data('date');			
			else
				var date = $(e.currentTarget).closest('.date-block').data('date');
			//找data-month.year
			var year = $('#calender').data('year');
			var month = $('#calender').data('month');	
			//插回panel（前端）
			$(panel.el).find('.month').text(month);
			$(panel.el).find('.date').text(date);
			//從form收集要傳給後端用的
			$(panel.el).find('[name= "year"]').val(year);
			$(panel.el).find('[name = "month"]').val(month);
			$(panel.el).find('[name= "date"]').val(date);
			 	
		},

		showError: function(msg){
			$(panel.el).find('.error-msg').addClass('open')
			.find('.alert').text(msg);
		},

		hideError: function(){
			$(panel.el).find('.error-msg').removeClass('open');
		}



	};


	//open panel to create / edit
	$('.date-block').dblclick(function(e){
			panel.open(true, e);   //open裡面記得要傳e進去，不然open function裡的top&left會抓不到位置
	}).on('dblclick', '.event', function(e){
			e.stopPropagation();
			panel.open(false, e);

			panel.selectedEvent =  $(e.currentTarget);

			var id = $(this).data('id');
			//AJAX call - find event detail by id
			$.post('event/read.php', { id: id}, function(data, textStatus, xhr){
			
				//load back event data 
				$(panel.el).find('[name="id"]').val(data.id);
				$(panel.el).find('[name="title"]').val(data.title);
				$(panel.el).find('[name="start_time"]').val(data.start_time);
				$(panel.el).find('[name="end_time"]').val(data.end_time);
				$(panel.el).find('[name="description"]').val(data.description);
			})
			.fail(function(xhr){
				panel.showError(xhr.responseText);
			});

		});



	$('#info-panel')
	.on('click', 'button', function(e){
		if($(this).is('.create') || $(this).is('.update')) {

			if($(this).is('.create'))
				var action = 'event/create.php';
			if($(this).is('.update'))
				var action = 'event/update.php';

			//collect data
			var data = $(panel.el).find('form').serialize(); //整合form資料

			//AJAX call - create API
			$.post(action, data)
				.done(function(data, textStatus, xhr){
						
					if($(e.currentTarget).is('.update'))
						panel.selectedEvent.remove();

				//insert into events template模板已在上面先準備好了（見最上面兩行）

				var eventUI = eventTemplate(data); //把準備好的資料塞進模板

				//Todo: insert event by time order

				panel.selectedDateBlock.find('.event').each(function(index, event){
										 //$(event)將參數變jquery物件才能取data，否則只是html
					var eventFromTime = $(event).data('from').split(':'); //10:00 > [10, 00]
					var newEeventFromTime = data.start_time.split(':'); //回傳回來的data是object所以用‘.’取值

					if(eventFromTime[0]>newEeventFromTime[0] || 
						eventFromTime[0]== newEeventFromTime[0] && eventFromTime[1]>newEeventFromTime[1]) {
						$(event).before(eventUI);
						return false; //因為在each function裡面，所以用此達成break，找到位置插入後就可停止each了
					}

				});
					//尋找自己的data-id(用字串相加帶入data.id），若長度為0代表newEvent時間最晚，還未加上去（否則上面就有加了）
				if(panel.selectedDateBlock.find('.event[data-id="'+data.id+'"]').length==0) 
					panel.selectedDateBlock.find('.events').append(eventUI);
				
				panel.close();

				})
				.fail(function(xhr, textStatus, errorThrown){
					panel.showError(xhr.responseText);

				});		  			
		}

		if($(this).is('.cancel')) {
			panel.close();
		}
		if($(this).is('.delete')) {
 			var result = confirm('Do you sure delete this?');

 			if(result) {
 				//find id
 				var id = panel.selectedEvent.data('id');

 				//AJAX call - delet php with id
 				$.post('event/delete.php', { id: id })
 				.done(function(){
 					//remove event form calender
 					panel.selectedEvent.remove();
 					panel.close();
 					
 				});
 			}	
			
		}

	})
	.on('click', '.close', function(e){
		$('button.cancel').click();
	});

});