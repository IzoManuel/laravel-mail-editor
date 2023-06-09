@extends('backend.layouts.app')

@section('title', 'Mailables')

@section('content')

<div class="col-lg-10 col-md-12">

                <div class="card my-4">
                    <div class="card-header d-flex align-items-center justify-content-between"><h5>{{ __('Mailables') }}</h5>

                        @if (!$mailables->isEmpty())
                            <a class="btn btn-primary" href="#newMailableModal" data-toggle="modal" data-target="#newMailableModal">{{ __('Add Mailable') }}</a>
                        @endif
                        <!-- Modal -->
                    </div>

                    @if ($mailables->isEmpty())

                    @component('maileclipse::layout.emptydata')

                        <span class="mt-4">{{ __("We didn't find anything - just empty space.") }}</span><button class="btn btn-primary mt-3" data-toggle="modal" data-target="#newMailableModal">{{ __('Add New Mailable') }}</button>

                    @endcomponent

                    @endif

                    @if (!$mailables->isEmpty())
                    <!---->
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ config('maileclipse.display_as_subject') ? __('Subject') : __('Namespace')}}</th>
                                <th>{{ __('Last edited') }}</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody >
                        @foreach($mailables->all() as $mailable)
                            <tr>
                                <td>
                                    {{ $mailable['name'] }}
                                </td>
                                <td >{{ config('maileclipse.display_as_subject') ? $mailable['subject'] : $mailable['namespace'] }}</td>

                                <td><span>{{ (\Carbon\Carbon::createFromTimeStamp($mailable['modified']))->diffForHumans() }}</span></td>

                                <td>
                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"  href="{{ route('viewMailable', ['name' => $mailable['name']]) }}" title="{{ translate('View') }}">
                                        <i class="las la-eye"></i>
                                    </a>

                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete remove-item table-action remove" data-mailable-name="{{ $mailable['name'] }}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                <div class="modal fade" id="newMailableModal" tabindex="-1" role="dialog" aria-labelledby="newMailableModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
  <form id="create_mailable" action="{{ route('generateMailable') }}" method="POST">
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Mailable</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-warning new-mailable-alerts d-none" role="alert">

        </div>
          <div class="form-group">
            <label for="mailableName">Name</label>
            <input type="text" class="form-control" id="mailableName" name="name" placeholder="Mailable name" required>
            <small class="form-text text-muted">Enter mailable name e.g <b>Welcome User</b>, <b>WelcomeUser</b></small>
          </div>
          <div class="form-group">
            <label class="checkbox-inline">
                <input type="checkbox" id="markdown--truth" value="option1"> Markdown Template
                <small class="form-text text-muted">Use markdown template</small>
            </label>
        </div>
        <div class="form-group markdown-input" style="display: none;">
            <label for="markdownView">Markdown</label>
            <input type="text" class="form-control" name="markdown" id="markdownView" placeholder="e.g markdown.view">
        </div>

        <div class="form-group">
            <label class="checkbox-inline">
                <input type="checkbox" id="forceCreation" name="force"> Force
                <small class="form-text text-muted">Force mailable creation even if already exists</small>
            </label>
        </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Create Mailable</button>
      </div>
    </div>
</form>
  </div>
</div>
            </div>

<script type="text/javascript">

    $(document).ready(function(){

        if ($('#markdown--truth').is(':checked')) {

                $('.markdown-input').show();
            } else {

                $('.markdown-input').hide();
            }

        $('#markdown--truth').change(
        function(){
            if ($(this).is(':checked')) {

                $('.markdown-input').show();
            } else {

                $('.markdown-input').hide();
            }
        });

    $('.remove-item').click(function(){
        var mailableName = $(this).data('mailable-name');

    notie.confirm({

        text: 'Are you sure you want to do that?<br>Delete Mailable <b>'+ mailableName +'</b>',

    submitCallback: function () {

    axios.post('{{ route('deleteMailable') }}', {
        mailablename: mailableName,
    })
    .then(function (response) {

        if (response.data.status == 'ok'){

            notie.alert({ type: 1, text: 'Mailable deleted', time: 2 });

            jQuery('tr#mailable_item_' + mailableName).fadeOut('slow');

            var tbody = $("#mailables_list tbody");

            if (tbody.children().length <= 1) {
                location.reload();
            }

        } else {
            notie.alert({ type: 'error', text: 'Mailable not deleted', time: 2 })
        }
    })
    .catch(function (error) {
        notie.alert({ type: 'error', text: error, time: 2 })
    });

  }
})

    });
});

    $('form#create_mailable').on('submit', function(e){
        e.preventDefault();
        // /generateMailable
        // new-mailable-alerts
        //
        //


    if ( $('input#markdown--truth').is(':checked') && $('#markdownView').val() == '')
    {

        // console.log('yes');
        $('#markdownView').addClass('is-invalid');
        return;
    }


        axios.post( $(this).attr('action'), $(this).serialize() )

        .then(function (response) {
            if (response.data.status == 'ok')
            {
                $('#newMailableModal').modal('toggle');
                notie.alert({ type: 1, text: response.data.message, time: 3 });

                setTimeout(function(){ location.reload(); }, 1000);
            } else {
                $('.new-mailable-alerts').text(response.data.message);
                $('.new-mailable-alerts').removeClass('d-none');
            }
        })

    .catch(function (error) {
        notie.alert({ type: 'error', text: error, time: 2 })
    });

    });


</script>

@endsection
