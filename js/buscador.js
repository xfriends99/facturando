/**
	**options to have following keys:
		**searchText: this should hold the value of search text
		**searchPlaceHolder: this should hold the value of search input box placeholder
**/
(function($){
	$.fn.tableSearch = function(){
		if(!$(this).is('table')){
			return;
		}
		var tableObj = $(this),
			inputObj = $("#kwd_search"),
			caseSensitive = false,
			searchFieldVal = '',
			pattern = '';
		inputObj.off('keyup').on('keyup', function(){
			searchFieldVal = $(this).val();
			pattern = (caseSensitive)?RegExp(searchFieldVal):RegExp(searchFieldVal, 'i');
			tableObj.find('tbody tr').hide().each(function(){
				var currentRow = $(this);
				currentRow.find('th:first').each(function(){
					if(pattern.test($(this).html())){
						currentRow.show();
						return false;
					}
				});
			});
		});
		return tableObj;
	}
}(jQuery));