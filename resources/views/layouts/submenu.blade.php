<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        {{$submenu->nome}}<span class="caret"></span>
    </a>
    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
    @foreach($submenu->submenu as $item)
        @if(isset($item->submenu))
            @include('layouts.submenu', ['submenu' => $item])
        @else
            <a class="dropdown-item" href="{{url($item->acao)}}">{{$item->nome}}</a>
        @endif
    @endforeach
    </div>
</li>
