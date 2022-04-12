let currentOption = 'welcome';
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
function viewExchange(newView){
    newView='newArticle';
    // Ocultar vista acutal si alguna
    if(currentOption){
        let currentView = getView(currentOption);
        if(currentOption !=newView){
            $("."+currentView).toggleClass('hide');
            $(".newArticleView").toggleClass('hide') ? currentOption='newArticle':'';
        }
        
    }
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
    console.log('Currrent view is '+ currentOption);
});

// Añadir nuevo  artículo
$("#newArticle").click(function () {
    // Ocultar la vista actual, si alguna
    url= $(this).attr('id');
    viewExchange(url);

});


//On New Article submit
$("#newArticle__form").submit(function (event) {
    container = event.target;
    console.log(container);
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
    console.log(newArticle);

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


  // Edit Article

  $("#editArticle").click(function () {
    // Ocultar la vista actual, si alguna
    console.log('Edit article');
	if(currentOption){ 
        console.log('hay una vista actualmente');
        let currentView = getView(currentOption);
        console.log('current option '+currentOption);
        if(currentOption !="editArticle"){
            console.log('La current option es diferente');
            $("."+currentView).toggleClass('hide') ? console.log('Se ocultó la vista actual'):'';
            $(".editArticlesView").toggleClass('hide') ? currentOption='editArticle':'';
        }
        $.ajax({
            url: "/api/check_user_session.php",
            success: function (success) {
                let result = JSON.parse(success);
                if (result.session == true) {
                    //Now send the data to php file to save item to cart using ajax
                    $.ajax({
                        url: "/api/articles.php",
                        contentType: false,
                        processData: false,
                        method: "get",
                        success: function (success) {
                            let articles = JSON.parse(success);
                            if (articles.total>0) {
                                console.log(articles);
                            }
                        }
                    });
                }
            }
        });
    }
  });