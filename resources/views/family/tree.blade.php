
@foreach ($tree as $node)
    <div class="p20">
    {{ !empty($node['fio']['fname']) ? $node['fio']['fname'] : 'Empty' }} + 

    @if (!empty($node['marriage']))
        @foreach ($node['marriage'] as $key => $partner)

            @if ($key=='anonim')
                Anonim
            @else
                {{ !empty($partner['fio']['fname']) ? $partner['fio']['fname'] : 'Empty' }}
            @endif

            @if (!empty($partner['children']))
                @include('family/tree', ['tree' => $partner['children']])
            @endif

        @endforeach
    @endif

    @if (!empty($node['children']))
        @include('family/tree', ['tree' => $node['children']])
    @endif
    </div>
@endforeach
