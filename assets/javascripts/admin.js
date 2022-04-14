let currentOption = 'welcome';
var page = 1;
let viewsClasses = {
    "welcome": "welcomeView",
    "newArticle": "newArticleView",
    "editArticle": "editArticlesView",
    "editStore": "editStoreView",
    "Usuarios": {
        "viewAllUsers": 'viewAllUsersView',
        'newUser': 'newUserView',
        'newUserView': 'changePasswordView',
    
    }
}


class Article {
    constructor() {
        this.name;
        this.tallas={};
        this.detalles;
        this.categoria;
        this.images=[];
        
    }

    addSize(size, av){
        !isNaN(size)? size = size.toString():'';
        (size!==undefined && !isNaN(av))?this.tallas[size]===undefined? this.tallas[size]=av:'':console.log('No se puede hacer eso');
    }
    rmSize(size){
        delete this.tallas[size];
    }
    setName(name){
        this.name=name;
    }
    setDetails(detalles){
        this.detalles=detalles;
    }
    setCategory(cat){
        this.categoria = cat;
    }
    updateSize(size, av){
        (!isNaN(size) && !isNaN(av))?this.tallas[size]=av:console.log('No se puede hacer eso');
        
    }


}

var newArticle= new Article;

function getAllViews(){
    let views=[];
    for (const key in viewsClasses) {
        if (viewsClasses.hasOwnProperty.call(viewsClasses, key)) {
            let element = viewsClasses[key];
            if(typeof(element)=='object'){
                for (const key in element) {
                    if (element.hasOwnProperty.call(element, key)) {
                        let view = element[key];
                        views.push(view);
                    }
                }
            }else{
                views.push(element);
            }
        }
    }
    return views;
    
}

function getView(option){
    return viewsClasses[option];

}

// Cambiar view
function viewExchange(url){
    // Ocultar vista acutal si alguna
    if(currentOption){
        let view = getView(url);
        if(currentOption !=view){
            $("."+currentOption).toggleClass('hide');
            $("."+view).toggleClass('hide') ? currentOption=view:'';
        }
        
    }
}

// Actualizar Articulo
function updateArticle(Event){
    Event.preventDefault();
    console.log(Event.target+' Was clicked');
    action = $(Event.target).data('action');
    id = $(Event.target).data('article-id');
    console.log("The action is "+action);
    nombre= $(Event.target).data('name');
    let editArticle = {};
    switch (action){
        case 'status':
            newStatus = $(Event.target).data('new-status');
            editArticle['active'] = newStatus;
            console.log(editArticle);
            $.ajax({
                url: "/api/check_user_session.php",
                success: function (success) {
                    let result = JSON.parse(success);
                    if (result.session == true) {
                        editArticle = JSON.stringify(editArticle);
                        //Now send the data to php file to save item to cart using ajax
                        $.ajax({
                            url: "/api/updateArticle.php",
                            method: "get",
                            data: {"id": id, "editArticle": editArticle},
                            success: function (success) {
                                let result = JSON.parse(success);
                                console.log(result.status);
                                if (result.status == 200) {
                                    //Actualizar el botón del status
                                    let action  = newStatus==0?'Ocultó': 'Publicó';
                                    let message = "Se "+action+ ' el artículo';
                                    Swal.fire(message);
                                    updateProductsList(page);
                                }
                            }
                        });
                    }else{
                        Swal.fire('No podemos hacer eso. Debes primero iniciar sesion.');
                    }
                }
            });
            break
        case 'del':
            console.log('Eliminar Ariticulo');
            Swal.fire({
                title: '¿Seguro quieres eliminar el artículo '+nombre+'?',
                text: "Este cambio es irreversible",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, Borrar!',
                cancelButtonText: 'Cancelar',
              }).then((result) => {
                if (result.isConfirmed) {
                    //Now send the data to php file to update the article
                    $.ajax({
                        url: "/api/check_user_session.php",
                        success: function (success) {
                            let result = JSON.parse(success);
                            if (result.session == true) {
                                editArticle = JSON.stringify(editArticle);
                                //Now send the data to php file to save item to cart using ajax
                                $.ajax({
                                    url: "/api/updateArticle.php",
                                    method: "get",
                                    data: {"del": id},
                                    success: function (success) {
                                        let result = JSON.parse(success);
                                        console.log(result.status);
                                        if (result.deleted == true) {
                                            //Actualizar el botón del status
                                            Swal.fire(
                                                'Borrado!',
                                                'El artículo ha sido borrado.',
                                                'success'
                                                )
                                            updateProductsList(page);
                                        }
                                    }
                                });
                            }else{
                                Swal.fire('No podemos hacer eso. Debes primero iniciar sesion.');
                            }
                        }
                    });
            }
            });
            break
    }
}

// Actualizar lista de productos
function updateProductsList(){
    $('.articlesList').find('.row.article').remove();
    $.ajax({
        url: "/api/articles.php?page="+page,
        contentType: false,
        processData: false,
        method: "get",
        success: function (success) {
            let articles = JSON.parse(success);
            if (articles.total>0) {
                //Display articles list
                for( let i=1; i <= articles.includes;i++){
                    var nombre = articles.products[i]['name'];
                    let id = articles.products[i]['id'];
                    let permalink = articles.products[i]['permalink'];
                    let isActive = articles.products[i]['active'];
                    let changeActive = isActive==1?0:1;
                    let statusText =  isActive==1?'Ocultar':'Publicar';
                    let status = '<a href="#hideArticle"  data-article-id="'+id+'"  data-name="'+nombre+'" data-new-status="'+changeActive+'" data-action="status" class="product-url-modal">'+statusText+'</a>';
                    let delArticle = '<a href="#delArticle" data-article-id="'+id+'"  data-name="'+nombre+'" data-action="del" class="product-url-modal" style="color:red">Eliminar</a>';
                    let editArticle = '<a href="#editArticle" data-article-id="'+id+'"  data-name="'+nombre+'" data-action="edit" class="product-url-modal">Editar</a>'
                    let firstImage = articles.products[i]['images'][0]['name'];
                    let product_info = articles.products[i]['info'];
                    let productInfo = product_info.substring(0, 80);
                    let number = i+((articles.page-1)*10);
                    let articleItem = ' <div class="row article hide">'+
                                            '<div class="col-sm article-number">'+
                                            number+
                                            '</div>'+
                                            '<div class="col-sm">'
                                            +'<a href="'+permalink+'"  class="product-url-modal" target="_blank">'+ nombre +'</a>'+
                                            '<p class="product-info">'+productInfo+'</p>'+
                                            '</div>'+
                                            '<div class="col-sm" style="margin-bottom:15px">'+
                                            '<img src="/uploads/images/thumbs/'+firstImage+'" style="width:80%;height:100%; display:flex;flex-aligment:center;margin-top:5px;margin-bottom:-80px">'+
                                            '</div>'+
                                            '<div class="col-sm article-action">'+
                                            status+editArticle+delArticle
                                            '</div>'+
                                        '</div>';
                    $(".articlesList").append(articleItem);
                }
                $(".article").removeClass('hide');
                // Capture user action to edit article
                $(".article-action a").click(function(Event){
                    updateArticle(Event);
                }
                );
                $(".loading-articles-list").addClass('hide');
                            // create pagination links
                $('.articles-pagination a').remove();
                pagina = articles.page;
                paginas =  Math.ceil(articles.total/articles.limit);

                let showing =0;
                // Pagination Backward
                let y = pagina;
                let x = pagina-6;
                pagina<5?x=pagina-1:'';
                for(let i=pagina; i < 5+pagina; i++ ){
                    pag = y-x;
                    if(pag>0 && pag < pagina){
                        showing++;
                        let link = '<a href="#page'+pag+'" data-page="'+pag+'" class="link">'+pag+'</a>';
                        $('.articles-pagination').not(".info").append(link);
                    }
                    x--;
                    if(i>paginas){
                        break;
                    }
                }
                            // Pagination Foward
                let extraPage = 0;
                pagina<5?extraPage=5-pagina:'';
                for(let i=pagina; i <= 5+pagina+extraPage; i++ ){
                    if(i>paginas){
                        break;
                    }
                    selected = pagina==i?'location':'';
                    pag = i;
                    let link = '<a href="#page'+pag+'" data-page="'+pag+'" class="link '+selected+'">'+pag+'</a>';
                    $('.articles-pagination').not(".info").append(link);
                    showing++;

                }
                $('.articles-pagination a').click(
                    function(){
                        page=$(this).data('page');
                        updateProductsList(page);
                    }
                );

                // Mostrar info sobre la paginación
                let paginationInfo = 'Mostrando página '+pagina+' de un total de '+paginas;
                $('.articles-pagination.info p').text(paginationInfo);

            }

        }
    });
}


// delete article size
function rmSize(e){
    $(e.target).parents(".size")[0].remove();
    let size = $(e.target).data("rm");
    newArticle.rmSize(parseInt(size));
    
}
// Hide welcome screen

$("body").click(function () {
    // Ocultar la vista actual, si alguna
	if(currentOption!='welcome'){
        if(!$(".welcome").hasClass('hide')){
            $(".welcome").toggleClass('hide');
        }
    }
    console.log(page);
});

// Menu Lateral Eventos

// Añadir nuevo  artículo
$("#newArticle").click(function () {
    // Ocultar la vista actual, si alguna
    url= $(this).attr('id');
    viewExchange(url);

});

  // Edit Article

  $("#editArticle").click(function () {
    // Ocultar la vista actual, si alguna
    url= $(this).attr('id');
    viewExchange(url);
    $(".loading-articles-list").removeClass('hide');
    $.ajax({
        url: "/api/check_user_session.php",
        success: function (success) {
            let result = JSON.parse(success);
            if (result.session == true) {
                //Now send the data to php file to save item to cart using ajax
                updateProductsList();
            }
        }
    });
});


//On New Article submit
$("#newArticle__form").submit(function (event) {
    container = event.target;
	event.preventDefault();
    if(Object.keys(newArticle.tallas).length>0){
        if(Object.keys(newArticle.images).length>0){
            var form = new FormData(jQuery('form')[0]);
            var imgs = document.querySelectorAll(".product_img");
            for (var i = 0; i < imgs.length; i++) {
                form.append("images[]", imgs[i].file);
            }
            form.append("sizes", JSON.stringify(newArticle.tallas))

            $.ajax({
                url: "/api/check_user_session.php",
                success: function (success) {
                    let result = JSON.parse(success);
                    if (result.session == true) {
                        //Now send the data to php file to save item to cart using ajax
                        $.ajax({
                            url: "/api/newArticle.php",
                            data: form,
                            contentType: false,
                            processData: false,
                            method: "post",
                            success: function (success) {
                                let result = JSON.parse(success);
                                if (result.status == 200) {
                                    Swal.fire({
                                        title: 'Se añadió el nuevo artículo',
                                        showCancelButton: true,
                                        confirmButtonText: 'Editar',
                                        cancelButtonText: 'Añadir Otro',
                                        html:
                                        'El link del artículo es <a target="_blank" class="product-url-modal" href="'+result.host+result.permalink+'"> ' +
                                        result.host+result.permalink+
                                        '</a>',
                                        icon: 'success',
                                      }).then((result) => {
                                        /* Read more about isConfirmed, isDenied below */
                                        if (result.isConfirmed) {
                                          Swal.fire('Saved!', '', 'success')
                                        } else if (result.isDenied) {
                                          Swal.fire('Changes are not saved', '', 'info')
                                        }
                                      })
                                    /*
                                    $( ".del-size" ).each(function( index ) {
                                        $(this).parents(".size")[0].remove();
                                    });
                                    $( ".rm-image" ).each(function( index ) {
                                        $(this).parents("li")[0].remove();
                                    });
                                    $(container).trigger("reset");
                                    
                                    var newArticle= new Article;
                                    */
                                    

                                }else{
                                    console.log('Error al agregar el artículo')
                                }
                            },
                            error: function (error) {},
                        });
                    } else {
                            $("#login__modal").modal("show");
                    }
                }, 
            });
        }else{
            alert('Agrega por lo menos una imagen');
            $(".img-selector")[0].click();
        }
    }else{
        alert('Agregar por lo menos una talla');
        $(".talla")[0].focus();
    }
});

//Upload Image button
function selectImage(){
    var el = document.getElementById("product_images");
    if (el) {
      el.click();
    }
}

// on image selected for product
$("#product_images").change(function () {
    var imageList = this.files;
    for (var i = 0; i < imageList.length; i++) {
        var file = imageList[i];
        var imageType = /image.*/;
        if (!file.type.match(imageType)) {
          continue;
        }
        var container = document.getElementsByClassName("product-preview-images")[0];
         
        var span =  document.createElement("a");
        span.textContent="X Quitar imagen";
        span.classList.add("rm-image");
        span.setAttribute("data-image-uri","")
        var li = document.createElement("li");
        var img = document.createElement("img");
        img.classList.add("product_img");
        img.classList.add("img-responsive");
        img.classList.add("inline-block")
        img.file = file;
        img.nombre = file.name;
        li.appendChild(span);
        li.appendChild(img);
        newArticle.images.push(file.name);
        container.appendChild(li);
    
        var reader = new FileReader();
        reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
        reader.readAsDataURL(file);        
      }
      var rmImages = document.getElementsByClassName("rm-image");
       for(let i=0; i<rmImages.length;i++){
        rmImages[i].addEventListener("click", rmPreviewImg, false );  
       }
       $(this).val("");
});


function rmPreviewImg(e){
    let imgName = $( e.target ).parent().find(".product_img")[0].file.name;
    newArticle.images = newArticle.images.filter(img => img!=imgName);
     $( e.target ).parent().remove();

}

$(".new-size").click(function (){
    let origen =  $(this).parents(".size")[0];
    container =$(origen).clone();
    let parent =$(this).parents(".sizes")[0];
    let talla = $(parent).children().find(".talla").val();
    let disponible = $(parent).children().find(".disponible").val(); 
    if(newArticle.tallas[talla]===undefined){
        if(talla==''){
            alert('Especifique la talla')
            $(parent).children().find(".talla").focus();
        }else if(disponible==''){
            alert('Ingrese la cantidad disponible de talla '+talla)
            $(parent).children().find(".disponible").focus()

        }else if(isNaN(disponible)){
            alert('Ingrese la cantidad en números de artículos disponibles de talla '+talla)
            $(parent).children().find(".disponible").val("");
            $(parent).children().find(".disponible").focus();
        }else{
            sizeViewUI();
        }
        function sizeViewUI(){
            var delSize = $("<span class='del-size' onClick='rmSize(event)' data-rm='"+talla+"'>X</span>");
            $(container).find(".new-size").parent().remove();
            $(container).find(".talla").prop('disabled', true);
            $(container).find(".disponible").prop('disabled', true);
            $(container).find(".talla").attr('id', talla);
            $(container).find(".disponible").toggleClass(disponible);
            let sizeInfo = $(container).find(".talla").clone();
            sizeInfo.val("Talla: "+talla+" | Disponible : "+disponible);
            sizeInfo.css('width','40%')
            $(container).find(".talla").parent().remove();
            $(container).find(".disponible").parent().remove();
            $(origen).find(".talla").val("");
            $(origen).find(".disponible").val("");
            $(".sizes").append(container);
            $(container).append(sizeInfo);
            $(container).append(delSize);
            newArticle.addSize(talla,parseInt(disponible));


        }
        
    }else{
        alert("Se actualizó la cantidad para la talla "+talla);
        newArticle.updateSize(talla,disponible);
        $(origen).find(".talla").val("");
        $(origen).find(".disponible").val("");
        $("#"+talla).val("Talla: "+talla+" | Disponible : "+disponible);
    }
});

  $( ".size" ).keypress(function(event) {
    event.preventDefault();
    let value = $(event.target).val()
    var keycode = event.keyCode || event.which;
    if(keycode == '13') {
        event.stopPropagation();   
        $(event.target).parents().find('.new-size').click();
    }else{
        $(event.target).val(value+event.key);
    }
  
  });

  $("#precio").change(function(){
      if(isNaN($(this).val())){
          alert('Indique el precio en números');
          this.focus();
      }
  });

