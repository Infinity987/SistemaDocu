<li @isset($item['id']) id="{{ $item['id'] }}" @endisset class="nav-header {{ $item['class'] ?? '' }}" style="background: linear-gradient(450deg, #000000, #432807);">

    {{ is_string($item) ? $item : $item['header'] }}

</li>
