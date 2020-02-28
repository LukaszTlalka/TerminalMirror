<table style="width:100%">
    @foreach ($fileContents as $file => $contents)
        <tr>
            <th>{{$file}}</th>
            <td><pre>{{ $contents }}</pre></td>
        </tr>
    @endforeach
</table>
