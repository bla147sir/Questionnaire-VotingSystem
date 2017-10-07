$(function(){
		$('.button').click(function() {
			var $div = $('div.text');
			// 先取得是否有記錄在 .data('contentHeight') 中
			var contentHeight = $div.data('contentHeight');
			
			// 若沒有記錄
			if(!!!contentHeight){
				// 取得完整的高
				contentHeight = determineActualHeight($div);
				// 並記錄在 .data('contentHeight') 中
				$div.data('contentHeight', contentHeight);
			}

			// 進行折疊
			$div.stop().animate({ 
				height: (contentHeight == $div.height() ?60: contentHeight)
			}, 1000);
		});

		function determineActualHeight($div) {
			var $clone = $div.clone().hide().css('height', 'auto').appendTo($div.parent()),
			height = $clone.height();
			$clone.remove();
			return height;
		}
	})