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
		let cat = $('#select__cat').val();
		let status = $('#select__status').val();

		if (products.length > 0 && url !== "" && cat !== "-1" && status !== "") {
			$.ajax({
				type: "post",
				url: moresell_ajaxurl.ajaxurl,
				data: {
					action: "export_products_to_child",
					nonce: moresell_ajaxurl.nonce,
					products: products,
					url: url,
					cat: cat,
					status: status
				},
				dataType: "json",
				beforeSend: () => {
					btn.prop('disabled', true);
					$('.btntxt').hide();
					$('.loading').css('visibility', 'visible');
				},
				success: function (response) {
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
				url: moresell_ajaxurl.ajaxurl,
				data: {
					action: "moresell_delete_url",
					nonce: moresell_ajaxurl.nonce,
					urlid: urlid
				},
				success: function (response) {
					btn.parent().parent().remove();
				}
			});
		}
	});

});
