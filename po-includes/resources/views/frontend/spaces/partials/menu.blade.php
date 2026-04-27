@if((count($menu->children) > 0) && ($menu->parent == 0))
   <li class="nav-item dropdown">
     <a href="{{ url($menu->url) }}" class="nav-link" data-toggle="dropdown" role="button">
      <span class="nav-link-inner-text mr-1">{!! $menu->title !!}</span> <i class="fas fa-angle-down nav-link-arrow"></i>
     </a>
 @else
 <li class="nav-item">
   @if($menu->target == 'none')
     <a href="{{ url($menu->url) }}" class="nav-link">
       {!! $menu->title !!}
     </a>
     @else
     <a href="{{ $menu->url }}" class="nav-link" target="_blank">
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