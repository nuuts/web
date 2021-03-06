<form method="POST" action="{{route('place.cover.store')}}" enctype="multipart/form-data">
    <label>Set cover:</label>
    <div class="form-group">
        {{ csrf_field() }}
        <div class="fileinput fileinput-new text-center" data-provides="fileinput">
            <div class="fileinput fileinput-new text-center" data-provides="fileinput">
                <div class="fileinput-new thumbnail">
                    <img src="{{asset('img/image_placeholder.jpg')}}" alt="...">
                </div>
            <div class="fileinput-preview fileinput-exists thumbnail" style=""></div>
            <div class="btn btn-default btn-fill btn-file">
                <span class="fileinput-new">Pick cover</span>
                <span class="fileinput-exists">Change cover</span>
                <input type="hidden">
                <input type="file" name="picture">
            </div>
        </div>
    <input class="btn btn-rose btn-wd btn-md" type="submit">
    </div>
</form>
