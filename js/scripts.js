document.addEvent('domready', function() {
	set_size();
});
window.addEvent('resize',function(){
	set_size();
});
function set_size()
{
	$('iClear').setStyle('height', window.getSize().y-$('bar').getScrollHeight()+'px');
}
