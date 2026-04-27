@if(count($menu->children) > 0)
    <li class="nav-item">
      <a href="{{ url($menu->url) }}" class="dropdown-item">
        {!! $menu->title !!}
      </a>
  @else
  <li class="nav-item">
    @if($menu->target == 'none')
      <a href="{{ url($menu->url) }}" class="dropdown-item">
        {!! $menu->title !!}
      </a>
      @else
      <a href="{{ $menu->url }}" class="dropdown-item" target="_blank">
        {!! $menu->title !!}
        <i class='bx bxs-chevron-right'></i>
      </a>
      @endif
  @endif
  @if (count($menu->children) > 0)
      <ul class="dropdown-menu">
        <li class="nav-item">
          @foreach($menu->children as $menu)
          @include(getTheme('partials.submenu'), $menu)
          @endforeach
        </li>
    </ul>
  @endif
  </li>
