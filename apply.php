<?php include("meta.inc.html");?>
	<title>申請借用 - 佛光大學座位預約系統</title>
<?php include("header.inc.html");?>
		<section class ="content-header">
			<h1>申請借用</h1>
			<ol class="breadcrumb">
				<li>佛光大學座位預約系統</li>
				<li class="active"><a href="apply.html">申請借用</a></li>
			</ol>	
		</section>
		
		<div id="apply_dialog" class="modal">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header"> 
						<button type="button" class = "close" data-dismiss="modal" aria-rabel="close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">確定送出嗎?</h4>
					</div>
					<div class="modal-body">
						<p>您申請的座位為 : <span></span></p>
						<p>您申請的月份為 : <span></span></p>
						<p>您的系所為：<span></span></p>
						<p>您輸入的申請人姓名為 : <span></span></p>
						<p>您輸入的連絡電話為 : <span></span></p>
						<p>確定要送出以上資料嗎 ? </p>
					</div>
					<div class = "modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
						<button type="button" class="btn btn-primary">確定</button>
					</div>

				</div>
			</div>
		</div>
		
		<section class = "content">
			<div class = "row">
				<div class = "col-md-12">
					<form id="from_apply" role = "form">
						<div id ="apply_failure" class = "alert alert-danger" style="display: none">
							<h4><i class="icon fa fa-user-times"></i>申請失敗 !</h4>
							請確認輸入的申請資訊是否正確。
						</div>
						<div class = "box box-primary">
							<div class = "box-body">
								<div class ="row">
									<div class = "col-md-6 form-group">
										<label for="seat">座位席次 : </label>
										<div class ="input-group">
											<div class ="input-group-addon"><i class ="fa fa-fw fa-map-maker"></i>
											</div>
											<select id="seat" name="seat" class="form-control">
												<option>請選擇座位席次</option>
											</select>
										</div>
									</div>
									<div class="col-md-6 form-group">
										<label for="date">月份 : </label> 
										<div class="input-group">
											<div class="input-group-addon"><i class="fa fa-fw fa-calender"></i></div>						
											<input id="date" name="date" class="form-control" required />
										</div>
									</div>
									<div class = "col-md-12 form-group-row">
										<div class="col-md-12">
											<label> 席位 : (紅色為不開放，橘色為該時段已經有人借用)</label>
										</div>
										<div class="col-md-12">
											<div class = "input-group btn-group" data-toggle="buttons">
												<label class = "btn btn-flat btn-default">
													<input type="radio" name="time_1" value="✓" />
													<span class = "label-time-name">座位</span>
												</label>
												<a class="btn btn-flat btn-info btn-select-none">取消</a>
											</div>
										</div>
									</div>
									<div class = "col-md-4 form-group">
										<label for = "applicant">申請人姓名 : </label>
										<div class = "input-group">
											<div class = "input-group-addon"><i class = "fa fa-fw fa-pencil"></i></div>
											<input id = "applicant" name = "applicant" class = "form-control" required/>
										</div>
									</div>
									<div class = "col-md-4 form-group">
										<label for = "phone">申請人連絡電話 : </label>
										<div class = "input-group">
											<div class = "input-group-addon"><i class = "fa fa-fw fa-phone"></i></div>
											<input id = "phone" name = "phone" class ="form-control" required/>
										</div>
									</div>
								</div>
							</div>
							<div class = "box-footer">
								<button type="submit" class = "btn btn-primary">送出申請</button>
							</div>
						</div>
					</form>
				</div>				
			</div>
		</section>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>
        <script src="/datepicker/js/bootstrap-datepicker.min.js"></script>
        <script src="/datepicker/locales/bootstrap-datepicker.zh-TW.min.js"></script>
        <script src="/js/apply.js?v=a409a4e"></script>
<?php include("footer.inc.html");?>