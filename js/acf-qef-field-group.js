!function r(d,u,i){function f(t,e){if(!u[t]){if(!d[t]){var n="function"==typeof require&&require;if(!e&&n)return n(t,!0);if(l)return l(t,!0);var o=new Error("Cannot find module '"+t+"'");throw o.code="MODULE_NOT_FOUND",o}var a=u[t]={exports:{}};d[t][0].call(a.exports,function(e){return f(d[t][1][e]||e)},a,a.exports,r,d,u,i)}return u[t].exports}for(var l="function"==typeof require&&require,e=0;e<i.length;e++)f(i[e]);return f}({1:[function(e,t,n){(function(e){"use strict";var t,a=(t="undefined"!=typeof window?window.jQuery:void 0!==e?e.jQuery:null)&&t.__esModule?t:{default:t};acf.add_action("add_field",function(e){e.closest('[data-type="repeater"],[data-type="flexible_content"]').length&&e.find('tr[data-name="show_column"],tr[data-name="show_column_weight"],tr[data-name="allow_quickedit"],tr[data-name="allow_bulkedit"]').remove()});function n(e,t){var n=(0,a.default)(t).prop("checked"),o=(0,a.default)(t).closest("td.acf-input");o.find('[data-name="show_column_sortable"] [type="checkbox"]').prop("disabled",!n),o.find('[data-name="show_column_weight"] [type="number"]').prop("readonly",!n)}(0,a.default)(document).on("change",'[data-name="show_column"] [type="checkbox"]',function(e){return n(0,e.target)}).ready(function(){return(0,a.default)('[data-name="show_column"] [type="checkbox"]').each(n)})}).call(this,"undefined"!=typeof global?global:"undefined"!=typeof self?self:"undefined"!=typeof window?window:{})},{}]},{},[1]);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvanMvYWNmLXFlZi1maWVsZC1ncm91cC9pbmRleC5qcyJdLCJuYW1lcyI6WyJyIiwiZSIsIm4iLCJ0IiwibyIsImkiLCJmIiwiYyIsInJlcXVpcmUiLCJ1IiwiYSIsIkVycm9yIiwiY29kZSIsInAiLCJleHBvcnRzIiwiY2FsbCIsImxlbmd0aCIsIjEiLCJtb2R1bGUiLCJfanF1ZXJ5Iiwid2luZG93IiwiZ2xvYmFsIiwiYWNmIiwiYWRkX2FjdGlvbiIsIiRlbCIsImNsb3Nlc3QiLCJmaW5kIiwicmVtb3ZlIiwic2V0X3NvcnRhYmxlX2Rpc2FibGVkIiwic2hvd19jb2xfaW5wIiwiY2hlY2tlZCIsImRlZmF1bHQiLCJwcm9wIiwiJHBhcmVudCIsImRvY3VtZW50Iiwib24iLCJ0YXJnZXQiLCJyZWFkeSIsImVhY2giXSwibWFwcGluZ3MiOiJDQUFBLFNBQUFBLEVBQUFDLEVBQUFDLEVBQUFDLEdBQUEsU0FBQUMsRUFBQUMsRUFBQUMsR0FBQSxJQUFBSixFQUFBRyxHQUFBLENBQUEsSUFBQUosRUFBQUksR0FBQSxDQUFBLElBQUFFLEVBQUEsbUJBQUFDLFNBQUFBLFFBQUEsSUFBQUYsR0FBQUMsRUFBQSxPQUFBQSxFQUFBRixHQUFBLEdBQUEsR0FBQUksRUFBQSxPQUFBQSxFQUFBSixHQUFBLEdBQUEsSUFBQUssRUFBQSxJQUFBQyxNQUFBLHVCQUFBTixFQUFBLEtBQUEsTUFBQUssRUFBQUUsS0FBQSxtQkFBQUYsRUFBQSxJQUFBRyxFQUFBWCxFQUFBRyxHQUFBLENBQUFTLFFBQUEsSUFBQWIsRUFBQUksR0FBQSxHQUFBVSxLQUFBRixFQUFBQyxRQUFBLFNBQUFkLEdBQUEsT0FBQUksRUFBQUgsRUFBQUksR0FBQSxHQUFBTCxJQUFBQSxJQUFBYSxFQUFBQSxFQUFBQyxRQUFBZCxFQUFBQyxFQUFBQyxFQUFBQyxHQUFBLE9BQUFELEVBQUFHLEdBQUFTLFFBQUEsSUFBQSxJQUFBTCxFQUFBLG1CQUFBRCxTQUFBQSxRQUFBSCxFQUFBLEVBQUFBLEVBQUFGLEVBQUFhLE9BQUFYLElBQUFELEVBQUFELEVBQUFFLElBQUEsT0FBQUQsRUFBQSxDQUFBLENBQUFhLEVBQUEsQ0FBQSxTQUFBVCxFQUFBVSxFQUFBSiw2QkNBQSxNQUFBSyxLQUFBLG9CQUFBQyxPQUFBQSxPQUFBLFlBQUEsSUFBQUMsRUFBQUEsRUFBQSxPQUFBLGtDQUdBQyxJQUFJQyxXQUFXLFlBQWEsU0FBRUMsR0FJeEJBLEVBQUlDLFFBQVEseURBQXlEVCxRQUV6RVEsRUFBSUUsS0FBSyxpSUFBaUlDLFdBUzlHLFNBQXhCQyxFQUEwQnZCLEVBQUd3QixHQUVsQyxJQUFNQyxHQUFVLEVBQUFYLEVBQUFZLFNBQUVGLEdBQWNHLEtBQUssV0FDcENDLEdBQVUsRUFBQWQsRUFBQVksU0FBRUYsR0FBY0osUUFBUSxnQkFFbkNRLEVBQVFQLEtBQUssd0RBQXdETSxLQUFLLFlBQVlGLEdBQ3RGRyxFQUFRUCxLQUFLLG9EQUFvRE0sS0FBSyxZQUFZRixJQUluRixFQUFBWCxFQUFBWSxTQUFFRyxVQUNBQyxHQUFHLFNBQVMsOENBQThDLFNBQUNsQyxHQUFELE9BQU8yQixFQUF1QixFQUFHM0IsRUFBRW1DLFVBQzdGQyxNQUFPLFdBQUEsT0FBTSxFQUFBbEIsRUFBQVksU0FBRSwrQ0FBK0NPLEtBQU1WIiwiZmlsZSI6ImFjZi1xZWYtZmllbGQtZ3JvdXAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24oKXtmdW5jdGlvbiByKGUsbix0KXtmdW5jdGlvbiBvKGksZil7aWYoIW5baV0pe2lmKCFlW2ldKXt2YXIgYz1cImZ1bmN0aW9uXCI9PXR5cGVvZiByZXF1aXJlJiZyZXF1aXJlO2lmKCFmJiZjKXJldHVybiBjKGksITApO2lmKHUpcmV0dXJuIHUoaSwhMCk7dmFyIGE9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitpK1wiJ1wiKTt0aHJvdyBhLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsYX12YXIgcD1uW2ldPXtleHBvcnRzOnt9fTtlW2ldWzBdLmNhbGwocC5leHBvcnRzLGZ1bmN0aW9uKHIpe3ZhciBuPWVbaV1bMV1bcl07cmV0dXJuIG8obnx8cil9LHAscC5leHBvcnRzLHIsZSxuLHQpfXJldHVybiBuW2ldLmV4cG9ydHN9Zm9yKHZhciB1PVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmUsaT0wO2k8dC5sZW5ndGg7aSsrKW8odFtpXSk7cmV0dXJuIG99cmV0dXJuIHJ9KSgpIiwiaW1wb3J0ICQgZnJvbSAnanF1ZXJ5JztcblxuXG5hY2YuYWRkX2FjdGlvbignYWRkX2ZpZWxkJywgKCAkZWwgKSA9PiB7XG5cblx0Ly8gcmVtb3ZlIHF1aWNrZWRpdCBvcHRpb25zIG9uIHJlcGVhdGVyL2ZsZXhpYmxlX2NvbnRlbnQgc3ViIGZpZWxkc1xuXG5cdGlmICggJGVsLmNsb3Nlc3QoJ1tkYXRhLXR5cGU9XCJyZXBlYXRlclwiXSxbZGF0YS10eXBlPVwiZmxleGlibGVfY29udGVudFwiXScpLmxlbmd0aCApIHtcblxuXHRcdCRlbC5maW5kKCd0cltkYXRhLW5hbWU9XCJzaG93X2NvbHVtblwiXSx0cltkYXRhLW5hbWU9XCJzaG93X2NvbHVtbl93ZWlnaHRcIl0sdHJbZGF0YS1uYW1lPVwiYWxsb3dfcXVpY2tlZGl0XCJdLHRyW2RhdGEtbmFtZT1cImFsbG93X2J1bGtlZGl0XCJdJykucmVtb3ZlKCk7XG5cblx0fVxufSk7XG5cblxuLyoqXG4gKlx0RGlzYWJsZSBzb3J0YWJsZSBjaGVja2JveCBpZiBjb2x1bW4gaXMgbm90IHZpc2libGVcbiAqL1xuY29uc3Qgc2V0X3NvcnRhYmxlX2Rpc2FibGVkID0gKCBpLCBzaG93X2NvbF9pbnAgKSA9PiB7XG5cblx0Y29uc3QgY2hlY2tlZCA9ICQoc2hvd19jb2xfaW5wKS5wcm9wKCdjaGVja2VkJyksXG5cdFx0JHBhcmVudCA9ICQoc2hvd19jb2xfaW5wKS5jbG9zZXN0KCd0ZC5hY2YtaW5wdXQnKTtcblxuXHQkcGFyZW50LmZpbmQoJ1tkYXRhLW5hbWU9XCJzaG93X2NvbHVtbl9zb3J0YWJsZVwiXSBbdHlwZT1cImNoZWNrYm94XCJdJykucHJvcCgnZGlzYWJsZWQnLCFjaGVja2VkKTtcblx0JHBhcmVudC5maW5kKCdbZGF0YS1uYW1lPVwic2hvd19jb2x1bW5fd2VpZ2h0XCJdIFt0eXBlPVwibnVtYmVyXCJdJykucHJvcCgncmVhZG9ubHknLCFjaGVja2VkKTtcblxufVxuXG4kKGRvY3VtZW50KVxuXHQub24oJ2NoYW5nZScsJ1tkYXRhLW5hbWU9XCJzaG93X2NvbHVtblwiXSBbdHlwZT1cImNoZWNrYm94XCJdJywoZSkgPT4gc2V0X3NvcnRhYmxlX2Rpc2FibGVkKCAwLCBlLnRhcmdldCApKVxuXHQucmVhZHkoICgpID0+ICQoJ1tkYXRhLW5hbWU9XCJzaG93X2NvbHVtblwiXSBbdHlwZT1cImNoZWNrYm94XCJdJykuZWFjaCggc2V0X3NvcnRhYmxlX2Rpc2FibGVkICkgKTtcblxuIl19
