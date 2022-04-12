// Activate background white on nav on scroll
let scrollpos = window.scrollY;
const header = document.querySelector("nav");
const header_height = header.offsetHeight;

const add_class_on_scroll = () => header.classList.add("active");
const remove_class_on_scroll = () => header.classList.remove("active");

let selectedSizes= {};


function selectSize(product_id, talla){
	if(!selectedSizes[product_id]){
		selectedSizes[product_id]=[talla];
	}
	else{
		selectedSizes[product_id].push(talla);
	}
}

function removeSize(product_id, talla){
	if(selectedSizes[product_id]){
		elmIndex = selectedSizes[product_id].indexOf(talla);
		if(elmInde=!false){

			selectedSizes[product_id] = selectedSizes[product_id].filter(size => size != talla);

		}
	}
}

function hasSelectedSizes(product_id){
	$sizes = selectedSizes[product_id].length
	return($sizes>0 ? true:false);
}

window.addEventListener("scroll", function () {
	scrollpos = window.scrollY;

	if (scrollpos >= header_height) {
		add_class_on_scroll();
	} else {
		remove_class_on_scroll();
	}
});

//Updade cart view in navbar
function updateCartView(){
	$.get( "/api/hasCart.php", function( data ) {
		if(data.result==false){
		 $(".nav__cart").removeClass("active");
		 $(".cart-subtotal").addClass("hidden");
		}else{
		 $(".nav__cart").addClass("active");
		 $(".cart-subtotal").removeClass("hidden");
		 $(".cart-subtotal").text(data.value)
		$( ".cart__icon" ).effect( "shake" );
		}
	  }, "json" );
}
// Activate smooth scrolling
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
	anchor.addEventListener("click", function (e) {
		e.preventDefault();
		document.querySelector(this.getAttribute("href")).scrollIntoView({
			behavior: "smooth",
		});
	});
});

// Reveal password
$("body").on("click", ".password__button", function () {
	$(this).find("i").toggleClass("fa-eye fa-eye-slash");
	let input = $(this).parents(".input-group").find("input");
	if (input.attr("type") === "password") {
		input.attr("type", "text");
	} else {
		input.attr("type", "password");
	}
});

//On login submit
$("#login__form").submit(function (event) {
	event.preventDefault();
	let form = $(this).serialize();
	$("#login__button").attr("disabled", true);
	$("#login__button").find(".spinner-border").fadeIn();

	$.ajax({
		method: "post",
		data: form,
		url: "api/login.php",
		success: function (success) {
			var result = JSON.parse(success);
			if (result.status == 200) {
				$("#login__email").removeClass("is-invalid");
				$("#login__password").removeClass("is-invalid");
				window.location.href = "./";
			} else {
				$("#login__email").addClass("is-invalid");
				$("#login__password").addClass("is-invalid");
				$("#login__button").attr("disabled", false);
				$("#login__button").find(".spinner-border").fadeOut();
			}
		},
		error: function (error) {},
	});
});


//On login submit
$("#whatsapp__form").submit(function (event) {
	event.preventDefault();
	let form = $(this).serialize();
	$("#whatsapp__button").attr("disabled", true);
	$("#whatsapp__button").find(".spinner-border").fadeIn();

	$.ajax({
		method: "post",
		data: form,
		url: "api/login-whatsapp.php",
		success: function (success) {
			var result = JSON.parse(success);
			if (result.status == 200) {
				$("#nombre_apellido").removeClass("is-invalid");
				$("#whatsapp").removeClass("is-invalid");
				window.location.href = "./";
			} else {
				$("#whatsapp").addClass("is-invalid");
				$("#whatsapp2").addClass("is-invalid");
				$("#whatsapp__button").attr("disabled", false);
				$("#whatsapp__button").find(".spinner-border").fadeOut();
			}
		},
		error: function (error) {},
	});
});


// On register submit
$("#register__form").submit(function (event) {
	event.preventDefault();
	let form = $(this).serialize();
	$("#register__button").attr("disabled", true);
	$("#register__button").find(".spinner-border").fadeIn();

	$.ajax({
		method: "post",
		data: form,
		url: "api/register.php",
		success: function (success) {
			let result = JSON.parse(success);
			if (result.status == 200) {
				$("#register__email").removeClass("is-invalid");
				$("#register__modal").modal("hide");
				Swal.fire({
					position: "center",
					icon: "success",
					title: "<span clas='text-white'>You have been registered.</span>",
					showConfirmButton: true,
					confirmButtonText: `Continue shopping`,
					allowOutsideClick: false,
					background: "#fff url(assets/images/bg.jpg)",
				}).then((result) => {
					if (result.isConfirmed) {
						window.location.href = "./";
					}
				});
			} else {
				$("#register__email").addClass("is-invalid");
				$("#register__button").attr("disabled", false);
				$("#register__button").find(".spinner-border").fadeOut();
			}
		},
		error: function (error) {},
	});
});

// on cart icon click
$(".add-cart").click(function () {
	let product_id = $(this).data("product-id");
	let added__to__cart = $(this).parents(".shoe__card__overlay").find(".added__to__cart");
	let tallas=JSON.stringify(selectedSizes);
	// check if user is logged in;
	console.log(product_id);
	$.ajax({
		url: "/api/check_user_session.php",
		success: function (success) {
			let result = JSON.parse(success);
			if (result.session == true) {
				//Now send the data to php file to save item to cart using ajax
				$.ajax({
					url: "api/save_cart_item.php",
					data: { product_id: product_id, tallas: tallas },
					method: "post",
					success: function (success) {
						let result = JSON.parse(success);
						if (result.status == 200) {
							$(".nav__cart").addClass("active");
							added__to__cart.fadeIn();
							rmElm=".remove__from__cart-"+product_id;
							$(rmElm).css("display", "block");
							addBtn = ".product-"+product_id;
							$(addBtn).css("display", "none");
							$tallas = 'div[class="tallas '+product_id+'"]';
							$($tallas).css("display", "none");
							updateCartView();
						}
					},
					error: function (error) {},
				});
			} else {
					$("#login__modal").modal("show");
			}
		},
	});
});

// on remove item
$(".rm-cart").click(function () {
	let product_id = $(this).data("product-id");
	let added__to__cart = $(this).parents(".shoe__card__overlay").find(".added__to__cart");
	// check if user is logged in;
	$.ajax({
		url: "api/check_user_session.php",
		success: function (success) {
			let result = JSON.parse(success);
			if (result.session == true) {
				//Now send the data to php file to save item to cart using ajax
				$.ajax({
					url: "api/rm-cart-item.php",
					data: { product_id: product_id },
					method: "post",
					success: function (success) {
						let result = JSON.parse(success);
						if (result.status == 200) {
							added__to__cart.fadeOut();
							rmElm=".remove__from__cart-"+product_id;
							$(rmElm).css("display", "none");
							addBtn = ".product-"+product_id;
							$(addBtn).css("display", "inline");
							$tallas = 'div[class="tallas '+product_id+'"]';
							$($tallas).css("display", "");
							updateCartView();

						
						}
					},
					error: function (error) {},
				});
			} else {
				window.location.href = "./";
			}
		},
	});
});

// on cart item change
$(".cart__item__quantity").change(function () {
	let cart_id = $(this).data("cart-id");
	let product_id = $(this).parents(".cart__list__item").data("shoe-id");
	let item_quantity = $(this).val();
	let item_total = $(this).parents(".card-body").find(".cart__item__total");

	let data = {
		cart_id: cart_id,
		product_id: product_id,
		item_quantity: item_quantity,
	};

	$.ajax({
		url: "api/change_cart_qty.php",
		method: "post",
		data: data,
		success: function (success) {
			let result = JSON.parse(success);
			item_total.text(`$ ${result.new_total}`);
			$(".cart__sub__total").text(`$ ${result.sub_total}`);
			$(".cart__grand__total").text(`$ ${result.grand_total}`);
			updateCartView();
		},
		error: function (error) {},
	});
});

// Remove product from cart list

$('.confirmModal').click(function(e) {
	e.preventDefault();              
	$.confirmModal('Are you sure to delete this?', function(el) {
	  console.log("Ok was clicked!")
	  //do delete operation
	});
  }); 

// on checkout
$(".checkout__button").click(function () {
	let grand_total = $("#grand__total").text();

	Swal.fire({
		title: "Escanea el siguiente código o escribe tu número de Nequi",
		text: "Escanea el código o escribe tu número de Nequi",
		confirmButtonColor: '#3085d6',
		confirmButtonText: 'Pagar',
		input: "text",
	  }).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "api/rm-cart.php",
				method: "post",
				data: data,
				success: function (success) {
					let result = JSON.parse(success);
					$(".cart__sub__total").text(`$ ${result.sub_total}`);
					$(".cart__grand__total").text(`$ ${result.grand_total}`);
					updateCartView();
					$( item ).remove();
				},
				error: function (error) {
					console.log('No se pudo completar la acción.')
				},
			});
		}
	  })
});

// Seleccionar talla
$(".talla-circle.unselected").click(function () {
	let product_id = $(this).data("product-id");
	let talla = $(this).data("product-size");
	console.log('Talla Seleccionada');
	$(this).removeClass('unselected');
	$(this).toggleClass('selected');
	if($(this).hasClass('selected')){
		//agregar elemento a la lista de tallas seleccionadas
		selectSize(product_id, talla);
		console.log('tiene la clase selected');
	}else{
		removeSize(product_id, talla);
		console.log('no tiene la clase selected');
	}
	if(hasSelectedSizes(product_id)){
		$(".add-cart.product-"+product_id).prop('disabled', false);
	}else{
		$(".add-cart.product-"+product_id).prop('disabled', true);
	}

});

$(".add-cart-container").click(function () {
	alert('Selecciona al menos una talla');
});

$(".rm-cart-list").click(function () {
	cart_id = $(this).data("cart-id");
	item= ".cart-list-item-"+cart_id;
	let data = {
		cart_id: cart_id,
	};
	Swal.fire({
		text: "¿Quieres eliminar este producto del carrito?",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: '#3085d6',
		cancelButtonColor: 'beige',
		confirmButtonText: 'Si',
		cancelButtonText: 'No'
	  }).then((result) => {
		if (result.isConfirmed) {
			$.ajax({
				url: "api/rm-cart.php",
				method: "post",
				data: data,
				success: function (success) {
					let result = JSON.parse(success);
					$(".cart__sub__total").text(`$ ${result.sub_total}`);
					$(".cart__grand__total").text(`$ ${result.grand_total}`);
					updateCartView();
					$( item ).remove();
				},
				error: function (error) {
					console.log('No se pudo completar la acción.')
				},
			});
		}
	  })
});

//Comprar por Whatsapp button



$(".btn-card-icon").click(function () {
	let product_id = $(this).data("product-id");
	let product_name = $(this).data("product-name");
	let image_url = $(this).data("product-image")
	let message = "Quiero comprar "+product_name +' '+image_url;
	console.log('The product id is '+product_id);
	// check if user is logged in;
	window.location.href = "https://wa.me/573228707697?text="+message;
	
});