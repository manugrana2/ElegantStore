<div class="modal fade" id="whatsapp__modal" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="login__modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h1 class="font-bold">Compra <span class="text-primary">mediante Whatsapp</span></h1>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Login form -->
                <form id="whatsapp__form" class="">
                    <!-- Email input -->
                    <div class="form-group">
                        <label for="nombre_apellido">Tu nombre y Apellido</label>
                        <input id="nombre_apellido" name="nombre_apellido" type="text" class="form-control form-control-lg"
                            required>
                        <div class="invalid-feedback">
                            Error, verifique el número de whatsapp.
                        </div>
                    </div>
                    <!-- Password input -->
                    <div class="form-group">
                        <label for="whatsapp">¿Cuál es tu número de teléfono?</label>
                        <div class="input-group input-group-lg mb-3">
                            <input id="whatsapp" name="whatsapp" type="text" class="form-control form-control-lg" required>
                        </div>
                        <label for="whatsapp">Confirma tu número de whatsapp</label>
                        <div class="input-group input-group-lg mb-3">
                            <input id="whatsapp2" name="whatsapp2" type="text" class="form-control form-control-lg" required>
                        </div>
                    </div>

                    <div class="modal-footer px-0 border-0 d-flex justify-content-between">
                        <span>
                            <button id="whatsapp__button" type="submit" class="btn btn-primary">
                                Comprar
                                <div class="spinner-border text-light ml-2" role="status" style="display: none;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </button>
                        </span>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>