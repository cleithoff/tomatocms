/* http://keith-wood.name/countdown.html
   Traditional Chinese initialisation for the jQuery countdown extension
   Written by Cloudream (cloudream@gmail.com). */
(function($) {
	$.countdown.regional['vi'] = {
		labels: ['Năm', 'Tháng', 'Tuần', 'Ngày', 'Giờ', 'Phút', 'Giây'],
		labels1: ['Năm', 'Tháng', 'Tuần', 'Ngày', 'Giờ', 'Phút', 'Giây'],
		compactLabels: ['na', 'th', 'tu', 'ng'],
		timeSeparator: ':',
		isRTL: false};
	$.countdown.setDefaults($.countdown.regional['vi']);
})(jQuery);
