@php
  $rand = rand();    
@endphp
<li style="border-left:solid 1px #ddd;">
  <input class="permission-checkbox" style="vertical-align:top;margin-left:-5px;cursor:pointer;" type="checkbox" id="{{ $rand }}" {{ in_array($menu['gate'], $permissions) ? 'checked' : null }} name="gates[]" value="{{ $menu['gate'] }}">
  <label for="{{ $rand }}" style="cursor:pointer;">
    <strong>{{ $menu['name'] ?? $menu['title'] }}</strong>
    <p class="mb-0 text-muted">{{ $menu['description'] ?? null }}</p>
  </label>

  @if(isset($menu['submenus']))
    <ul>
      {!! $viewMenu($menu['submenus']) !!}
    </ul>
  @endif

  @if(isset($menu['gates']))
    <ul>
      {!! $viewMenu($menu['gates']) !!}
    </ul>
  @endif
</li>