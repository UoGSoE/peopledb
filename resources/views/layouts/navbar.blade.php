<nav class="navbar is-info" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item has-text-weight-semibold" href="/">
            PeopleDB
        </a>

        <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-start">

            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link">
                    More
                </a>

                <div class="navbar-dropdown">
                    <a class="navbar-item" href="{{ route('reports.people') }}">
                        People
                    </a>
                    <a class="navbar-item" href="{{ route('reports.stats') }}">
                        Stats/Trends
                    </a>
                    <hr class="navbar-divider">
                    <a class="navbar-item" href="{{ route('units.index') }}">
                        Units/Tasks
                    </a>
                    <a class="navbar-item" href="{{ route('options.edit') }}">
                        Options
                    </a>
                    <a class="navbar-item" href="{{ route('home') }}">
                        Manage Access
                    </a>
                </div>
            </div>
        </div>

        @auth
        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <form method="POST" action="/logout">
                        @csrf
                        <button class="button is-dark">Log Out {{ auth()->user()->full_name }}</button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</nav>
