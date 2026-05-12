<div class="card">
    <div class="card-header">
        <h6 class="mg-b-0">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    @foreach($fields as $field => $label)
                        <tr>
                            <th>{{ $label }}</th>
                            <td>{{ $menuharian->{$field} !== null ? number_format($menuharian->{$field}, 2) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
