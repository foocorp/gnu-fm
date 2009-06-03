{include file='header.tpl'}
{if ($logged_in)}
<h2>Welcome, <a href="{$this_user->getURL()}">{$this_user->name}</a>.</h2>
</div>
<div class="yui-gc"> 
    <div class="yui-u first">
    foo
    </div>
    <div class="yui-u">   
bar
    </div>
</div>
{else}
<h2>A better deal for artists and users.</h2>
       <h3>Libre.fm allows you to share your listening habits and discover new music.</h3>
</div>
<div class="yui-g"> 
    <div class="yui-u first" id="artist-box">

    <h2>For artists</h2>

    <p>We're building the best tools possible for the next generation
    of musicial talent &mdash; from direct communications with your
    fans that <strong>you</strong> can control, to the most advanced
    audio and video integration on the web ever.</p>

    <p>Libre.fm is built with privacy and freedom as features and
    goals, not things to worry about at a later date. As a Libre
    artist, you set yourself apart from the crowd by contributing to a
    commons of talent and creativity that will be the future of
    music. It is our job to create the tools and opportunities for you
    to directly speak to the people who like your music and want to
    support you as an artist.</p>

    <p>As a Libre musician, you can directly influence how the tools
    you use are developed. We believe this freedom is important, and
    want to do the best we can, for you and for our users.</p>

    </div>
    <div class="yui-u" id="user-box">   

    <h2>For users</h2>

    <p>Take control of your data. Take control of your listening
    habits and take control of your privacy.</p>

    <p>Use Libre.fm, or download our software and run your own server
    or website.</p>

    <p>Join Libre.fm and get access to wealth of new music from
    artists who contribute to a social pool of music that you can
    download, copy, swap and re-use without restriction.</p>
    
    </div>
</div>

{/if}

<div class="yui-g">
    <h3 class="signup-link"><a href="/register.php?t=artist">Join us now</a></h3>

    <p class="c"><small>It's free and very easy.</small></p>
</div>

{include file='footer.tpl'}
