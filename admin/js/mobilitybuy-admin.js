jQuery(function ($) {
	'use strict';

	$('#urls_table').DataTable();

	$('#select_all').on("click", function () {
		$('.select_product_item').each(function () {
			if ($(this).prop('checked') !== true) {
				$(this).prop('checked', true);
			} else {
				$(this).prop('checked', false)
			}
		});
	});

	$('#exportbtn').on("click", function () {
		let btn = $(this);
		let products = [];
		$('.select_product_item:checked').each(function () {
			products.push(
				$(this).val()
			);
		});
		
		let url = $('#select__site').val();
		let status = $('#select__status').val();
		let bonus_rate = $('#bonus_rate').val();
		let cats = $(".catlist input:checkbox:checked");

		let catlists = [];
		cats.each(function () {
			catlists.push({
				id: $(this).val(),
				parent: $(this).attr('parent'),
				name: $(this).attr('catname'),
			})
		});

		if (products.length > 0 && url !== "" && status !== "") {
			$.ajax({
				type: "post",
				url: mobilitybuy_ajaxurl.ajaxurl,
				data: {
					action: "export_products_to_child",
					nonce: mobilitybuy_ajaxurl.nonce,
					products: products,
					url: url,
					bonus_rate: bonus_rate,
					catlists: catlists,
					status: status
				},
				dataType: "json",
				beforeSend: () => {
					btn.prop('disabled', true);
					$('.btntxt').hide();
					$('.loading').css('visibility', 'visible');
				},
				success: function (response) {
					location.reload();
					btn.removeAttr('disabled');
					$('.btntxt').show();
					$('.loading').css('visibility', 'hidden');
				}
			});
		} else {
			alert("Select All fields!")
		}
	});

	// Delete urls
	$(document).on("click", '.delete_url', function () {
		if (confirm("It will completely be deleted!")) {
			let btn = $(this);
			let urlid = $(this).attr('data-id');
			$.ajax({
				type: "post",
				url: mobilitybuy_ajaxurl.ajaxurl,
				data: {
					action: "mobilitybuy_delete_url",
					nonce: mobilitybuy_ajaxurl.nonce,
					urlid: urlid
				},
				success: function (response) {
					btn.parent().parent().remove();
				}
			});
		}
	});

	// Get bonus val
	$('.select_product_item').each(function () {
		$(this).on("click", function () {
			if($(this).prop("checked") == true){
				let dataid = $(this).attr('data-id');
				$('#bonus_rate').val(dataid);
            }
		});
	});
	

});
