@extends('layouts.app')

@section('content')

    <div class="site-wrapper">
        <div class="site-wrapper-inner text-white text-center">
            <i class="fas fa-spinner fa-pulse fa-4x"></i>
        </div>
    </div>

    <main class="container-fluid">
        @if(session('updated'))
            <div class="alert alert-dark" role="alert">
                {{ session('updated') }}
            </div>
        @endif
        @isset($album)
            <h2 class="text-title mb-3">{{ $album->name }}</h2>
        @endif
        @isset($category)
            <h2 class="text-title mb-3">{{ $category->name }}</h2>
        @endif
        @isset($user)
            <h2 class="text-title mb-3">{{ __('Photos de ') . $user->name }}</h2>
        @endif
        <div class="d-flex justify-content-center">
            {{ $images->links() }}
        </div>
        <div class="card-columns">
            @foreach($images as $image)
                <div class="card @if($image->adult) border-danger @endif" id="image{{ $image->id }}">
                    <a href="{{ url('images/' . $image->name) }}" class="image-link" data-link="{{ route('image.click', $image->id) }}">                       <img class="card-img-top"
                             src="{{ url('thumbs/' . $image->name) }}"
                             alt="image">
                    </a>
                    @isset($image->description)
                        <div class="card-body">
                            <p class="card-text">{{ $image->description }}</p>
                        </div>
                    @endisset
                    <div class="card-footer text-muted">
                        <em>
                            <a href="{{ route('user', $image->user->id) }}" data-toggle="tooltip"
                               title="{{ __('Voir les photos de ') . $image->user->name }}">{{ $image->user->name }}</a>
                        </em>
                        <div class="pull-right">
                            <em>
                                (<span class="image-click">{{ $image->clicks }}</span> {{ trans_choice(__('vue|vues'), $image->clicks) }})
                                {{ $image->created_at->formatLocalized('%x') }}
                            </em>
                        </div>
                        <div class="star-rating" id="{{ $image->id }}">
                            <span class="count-number">({{ $image->users->count() }})</span>
                            <div id="{{ $image->id . '.5' }}" data-toggle="tooltip" title="5" @if($image->rate > 4) class="star-yellow" @endif>
                                <i class="fas fa-star"></i>
                            </div>
                            <div id="{{ $image->id . '.4' }}" data-toggle="tooltip" title="4" @if($image->rate > 3) class="star-yellow" @endif>
                                <i class="fas fa-star"></i>
                            </div>
                            <div id="{{ $image->id . '.3' }}" data-toggle="tooltip" title="3" @if($image->rate > 2) class="star-yellow" @endif>
                                <i class="fas fa-star"></i>
                            </div>
                            <div id="{{ $image->id . '.2' }}" data-toggle="tooltip" title="2" @if($image->rate > 1) class="star-yellow" @endif>
                                <i class="fas fa-star"></i>
                            </div>
                            <div id="{{ $image->id . '.1' }}" data-toggle="tooltip" title="1" @if($image->rate > 0) class="star-yellow" @endif>
                                <i class="fas fa-star"></i>
                            </div>
                            <span class="pull-right">
                                @adminOrOwner($image->user_id)
                                    <a href="#" class="toggleIcons">
                                        <i class="fa fa-cog"></i>
                                    </a>
                                    <span class="menuIcons" style="display: none">
                                        <a href="{{ route('image.destroy', $image->id) }}" class="form-delete text-danger" data-toggle="tooltip" title="@lang('Supprimer cette photo')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <a href="{{ route('image.description', $image->id) }}" class="description-manage" data-toggle="tooltip" title="@lang('Gérer la description')">
                                            <i class="fa fa-comment"></i>
                                        </a>
                                        <a href="{{ route('image.albums', $image->id) }}" class="albums-manage" data-toggle="tooltip" title="@lang('Gérer les albums')">
                                            <i class="fa fa-folder-open"></i>
                                        </a>
                                        <a href="{{ route('image.update', $image->id) }}" class="category-edit" data-id="{{ $image->category_id }}" data-toggle="tooltip" title="@lang('Changer de catégorie')">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="{{ route('image.adult', $image->id) }}" class="adult-edit" data-toggle="tooltip" title="@lang('Changer de statut')">
                                            <i class="fa @if($image->adult) fa-graduation-cap @else fa-child @endif"></i>
                                        </a>
                                    </span>
                                    <form action="{{ route('image.destroy', $image->id) }}" method="POST" class="hide">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endadminOrOwner
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $images->links() }}
        </div>

        <!-- modal changement description -->
        <div class="modal fade" id="changeDescription" tabindex="-1" role="dialog" aria-labelledby="descriptionLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="descriptionLabel">@lang('Changement de la description')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="descriptionForm" action="" method="POST">
                            <div class="form-group">
                                <input type="text" class="form-control" name="description" id="description">
                                <small class="invalid-feedback"></small>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('Envoyer')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
         <!-- fin -->

        <!-- modal changement catégorie -->
        <div class="modal fade" id="changeCategory" tabindex="-1" role="dialog" aria-labelledby="categoryLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryLabel">@lang('Changement de la catégorie')</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">
                                &times;
                            </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" id="editForm">
                            @method('PUT')
                            @csrf
                            <div class="form-group">
                                <select name="category_id" class="form-control" id="">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('Envoyer')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- fin -->

        <!-- modal formulaire vue albums -->
        <div class="modal fade" id="editAlbums" tabindex="-1" role="dialog" aria-labelledby="albumLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="albumLabel">@lang("Gestion des albums pour l'image")</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="POST" id="manageAlbums">
                            <div class="form-group" id="listeAlbums"></div>
                            <button type="submit" class="btn btn-primary">@lang('Envoyer')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- fin -->
    </main>

@endsection

@section('script')

    <script>
        $(() => {

            $('.site-wrapper').fadeOut(1000)

            $('[data-toggle="tooltip"]').tooltip()

            $('.card-columns').magnificPopup({
                delegate: 'a.image-link',
                type: 'image',
                tClose: '@lang("Fermer (Esc)")'@if($images->count() > 1),
                gallery: {
                    enabled: true,
                    tPrev: '@lang("Précédent (Flèche gauche)")',
                    tNext: '@lang("Suivant (Flèche droite)")'
                },
                callbacks: {
                    buildControls: function () {
                        this.contentContainer.append(this.arrowLeft.add(this.arrowRight))
                    }
                }@endif
            })
        })

        // afficher / cacher boite à outil des images
        $('a.toggleIcons').click((e) => {
                e.preventDefault();
                let that = $(e.currentTarget)
                that.next().toggle('slow').end().children().toggleClass('fa-cog').toggleClass('fa-play')
        })

        // Script pour la suppression d'image
        $('a.form-delete').click((e) => {
            e.preventDefault();
            let href = $(e.currentTarget).attr('href')
            swal({
                title: '@lang('Vraiment supprimer cette photo ?')',
                type: 'error',
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtomText: '@lang('Oui')',
                cancelButtonText: '@lang('Non')'
            }).then((result) => {
                if (result.value) {
                    $("form[action='" + href + "'").submit()
                }
            })
        })

        // Script modification description image
        const swallALertServer = () => {
            swal({
                title: '@lang('Il semble y avoir une erreur sur le serveur, veuillez réessayer plus tard...')',
                type: 'warning'
            })
        }

        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        })

        $('a.description-manage').click((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            let text = that.parents('.card').find('.card-text').text()
            $('#description').val(text)
            $('#descriptionForm').attr('action', that.attr('href')).find('input').removeClass('is-invalid').next().text()
            $('#changeDescription').modal('show')
        })

        $('#descriptionForm').submit((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            $.ajax({
                method: 'put',
                url: that.attr('action'),
                data: that.serialize()
            })
                .done((data) => {
                    let card = $('#image' + data.id)
                    let body = card.find('.card-body')
                    if(body.length) {
                        body.children().text(data.description)
                    } else {
                        card.children('a').after('<div class="card-body"><p class="card-text">' + data.description + '</p> </div>')
                    }
                    $('#changeDescription').modal('hide')
                })
                .fail((data) => {
                    if(data.status === 422) {
                        $.each(data.responseJSON.errors, function (key, value) {
                            $('#descriptionForm input[name=' + key + ']').addClass('is-invalid').next().text(value)
                        })
                    } else {
                        swallALertServer()
                    }
                })
        })
        // fin
        // Script modification catégorie image
        $('a.category-edit').click((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            $('select').val(that.attr('data-id'))
            $('#editForm').attr('action', that.attr('href'))
            $('#changeCategory').modal('show')
        })
        // Fin
        // Script modification type image
        $('a.adult-edit').click((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            let icon = that.children()
            let adult = icon.hasClass('fa-graduation-cap')
            if(adult) {
                icon.removeClass('fa-graduation-cap')
            } else {
                icon.removeClass('fa-child')
            }
            icon.addClass('fa-cog fa-spin')
            adult = !adult
            $.ajax({
                method: 'put',
                url: that.attr('href'),
                data: { adult: adult }
            })
                .done(() => {
                    that.tooltip('hide')
                    let icon = that.children()
                    icon.removeClass('fa-cog fa-spin')
                    let card = that.parents('.card')
                    if(adult) {
                        icon.addClass('fa-graduation-cap')
                        card.addClass('border-danger')
                    } else {
                        icon.addClass('fa-child')
                        card.removeClass('border-danger')
                    }
                })
                .fail(() => {
                    swallAlertServer()
                })
        })
        // Fin
// Script btn gérer les  albums
        
$('a.albums-manage').click((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            that.tooltip('hide')
            that.children().removeClass('fa-folder-open').addClass('fa-cog fa-spin')
            e.preventDefault()
            $.get(that.attr('href'))
                .done((data) => {
                    that.children().addClass('fa-folder-open').removeClass('fa-cog fa-spin')
                    $('#listeAlbums').html(data)
                    $('#manageAlbums').attr('action', that.attr('href'))
                    $('#editAlbums').modal('show')
                })
                .fail(() => {
                    that.children().addClass('fa-folder-open').removeClass('fa-cog fa-spin')
                    swallAlertServer()
                })
        })
        // Fin

        
        // Script soumission gérer les  albums

        $('#manageAlbums').submit((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            $.ajax({
                method: 'put',
                url: that.attr('action'),
                data: that.serialize()
            })
                .done((data) => {
                    if(data === 'reload') {
                        location.reload();
                    } else {
                        $('#editAlbums').modal('hide')
                    }
                })
                .fail(() => {
                    swallAlertServer()
                })
        })

        // Fin

        // Script gestion rating

        let memoStars = []
        $('.star-rating div').click((e) => {
            @auth
                let element = $(e.currentTarget)
                let values = element.attr('id').split('.')
                element.addClass('fa-spin')
                $.ajax({
                    url: "{{ url('rating') }}" + '/' + values[0],
                    type: 'PUT',
                    data: {value: values[1]}
                })
                .done((data) => {
                    if (data.status === 'ok') {
                        let image = $('#' + data.id)
                        memoStars = []
                        image.children('div')
                            .removeClass('star-yellow')
                            .each(function (index, element) {
                                if (data.value > 4 - index) {
                                    $(element).addClass('star-yellow')
                                    memoStars.push(true)
                                }
                                memoStars.push(false)
                            })
                            .end()
                            .find('span.count-number')
                            .text('(' + data.count + ')')
                        if(data.rate) {
                            if(data.rate == values[1]) {
                                title = '@lang("Vous avez déjà donné cette note !")'
                            } else {
                                title = '@lang("Votre vote a été modifié !")'
                            }
                        } else {
                            title = '@lang("Merci pour votre vote !")'
                        }
                        swal({
                            title: title,
                            type: 'warning'
                        })
                    } else {
                        swal({
                            title: '@lang('Vous ne pouvez pas voter pour vos photos !')',
                            type: 'error'
                        })
                    }
                    element.removeClass('fa-spin')
                })
                .fail(() => {
                    swallAlertServer()
                    element.removeClass('fa-spin')
                })
            @else
                swal({
                    title: '@lang('Vous devez être connecté pour pouvoir voter !')',
                    type: 'error'
                })
            @endauth
        })
        $('.star-rating').hover(
            (e) => {
                memoStars = []
                $(e.currentTarget).children('div')
                    .each((index, element) => {
                        memoStars.push($(element).hasClass('star-yellow'))
                    })
                    .removeClass('star-yellow')
            }, (e) => {
            $.each(memoStars, (index, value) => {
                if(value) {
                    $(e.currentTarget).children('div:eq(' + index + ')').addClass('star-yellow')
                }
            })
        })
        // Fin

        // gérer pluriel nobmre de vue

        $('a.image-link').click((e) => {
            e.preventDefault()
            let that = $(e.currentTarget)
            $.ajax({
                method: 'patch',
                url: that.attr('data-link')
            }).done((data) => {
                if(data.increment) {
                    let numberElement = that.siblings('div.card-footer').find('.image-click')
                    numberElement.text(parseInt(numberElement.text()) + 1)
                }
            })
        })

        // Fin
    </script>
@endsection
