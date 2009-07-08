            <h3>Explore popular artists</h3>
            <ul class="tagcloud">
    {section name=i loop=$tagcloud}
                <li style='font-size:{$tagcloud[i].size}'><a href='/artist/{$tagcloud[i].artist|urlencode}' title='This artist was played {$tagcloud[i].count} times' rel='tag'>{$tagcloud[i].artist}</a></li>
    {/section}
            </ul>