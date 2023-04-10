@extends('backend.layouts.app')

@section('title', 'Templates')

@section('content')


<div class="col-lg-10 col-md-12">
  
                <div class="card my-4">
                    <div class="card-header d-flex align-items-center justify-content-between"><h5>{{ __('Templates') }}</h5>
                        @if (!$templates->isEmpty())
                        <a href="{{ route('selectNewTemplate') }}" class="btn btn-primary">{{ __('Add Template') }}</a>
                        @endif
                    </div>

                    @if ($templates->isEmpty())
                    
                    @component('maileclipse::layout.emptydata')
                        
                        <span class="mt-4">{{ __("We didn't find anything - just empty space.") }}</span>
                        <a class="btn btn-primary mt-3" href="{{ route('selectNewTemplate') }}">{{ __('Add New Template') }}</a>

                    @endcomponent

                    @endif

                    @if (!$templates->isEmpty())
                    <!---->
                    <table id="templates_list">
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Template') }}</th>
                                <th>{{ __('') }}</th>
                                <th>{{ __('Type') }}</th>
                                <th>options</th>

                            </tr>
                        </thead>
                        <tbody>
                        @foreach($templates->all() as $template)
                            <tr id="template_item_{{ $template->template_slug }}">
                                <td class="pr-0">{{ ucwords($template->template_name) }}</td>
                                <td class="text-muted" title="/tee">{{ $template->template_description }}</td>

                                <td class="table-fit"><span>{{ ucfirst($template->template_view_name) }}</td>


                                <td class="table-fit text-muted"><span>{{ ucfirst($template->template_skeleton) }}</td>

                                <td class="table-fit text-center"><span>{{ ucfirst($template->template_type) }}</td>

                                <td class="table-fit">
                                    <a href="{{ route('viewTemplate', [ 'templatename' => $template->template_slug ]) }}" class="table-action mr-3">
                                    </a>
                                    <a href="#" class="table-action remove-item" data-template-slug="{{ $template->template_slug }}" data-template-name="{{ $template->template_name }}">
                                    </a>
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('viewTemplate', [ 'templatename' => $template->template_slug ]) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>

                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete remove-item table-action remove" data-mailable-name="{{ $template->template_name }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                    <!---->
                </div>
            </div>

<script type="text/javascript">

    $('.remove-item').click(function(){
        var templateSlug = $(this).data('template-slug');
        var templateName = $(this).data('template-name');

    notie.confirm({

        text: 'Are you sure you want to do that?<br>Delete Template <b>'+ templateName +'</b>',

    submitCallback: function () {

    axios.post('{{ route('deleteTemplate') }}', {
        templateslug: templateSlug,
    })
    .then(function (response) {

        if (response.data.status == 'ok'){
            notie.alert({ type: 1, text: 'Template deleted', time: 2 });

            jQuery('tr#template_item_' + templateSlug).fadeOut('slow');

            var tbody = $("#templates_list tbody");

            console.log(tbody.children().length);

            if (tbody.children().length <= 1) {
                location.reload();
            }

        } else {
            notie.alert({ type: 'error', text: 'Template not deleted', time: 2 })
        }
    })
    .catch(function (error) {
        notie.alert({ type: 'error', text: error, time: 2 })
    });

  }
})

    });


                
</script>
   
@endsection