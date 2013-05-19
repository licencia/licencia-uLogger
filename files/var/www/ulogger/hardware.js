// Show/hid IP info
$("#show-ip-btn").click(function() {
  $("#show-ip").hasClass('hidden') ? $("#show-ip").removeClass('hidden') : $("#show-ip").addClass('hidden');
  return false;
});

$('#popover-ram').popover({
	html : true,
	placement : 'bottom',
	trigger : 'hover',
	title : function() {
		return $("#popover-ram-head").html();
	},
	content : function() {
		return $("#popover-ram-body").html();
	}
});

$('#popover-cpu').popover({
	html : true,
	placement : 'bottom',
	trigger : 'hover',
	title : function() {
		return $("#popover-cpu-head").html();
	},
	content : function() {
		return $("#popover-cpu-body").html();
	}
});

