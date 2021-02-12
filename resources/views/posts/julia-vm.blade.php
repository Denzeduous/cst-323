@extends('layouts/main_layout')

@section('content')
<div style="width: 70%; margin-left: 15vw">
<p align="center"><a href="https://github.com/F0x1fy/julia-virtualized-cpu"><img src="https://gh-card.dev/repos/F0x1fy/julia-virtualized-cpu.svg" alt="F0x1fy/julia-virtualized-cpu - GitHub"></a></p>
<h1 id="julia-vm-julia-virtualized-cpu-">Julia VM (Julia Virtualized CPU)</h1>
<p>We&#39;re returning to an old project that I&#39;m still proud of to this day, as it marked a huge milestone in terms of my growth as a software developer. I hope you guys enjoy! This is an updated version of the original README in the GitHub repo.</p>
<p>This was made as a fun little side project to learn Julia, and is not meant to be a faithful recreation of Assembly or how a CPU truly works. While it is faithful to some portions, it&#39;s not meant to be a 100% perfect virtualization. There are many limitations that I may decide to change, depending on if I feel the want to come back to this project.</p>
<h1 id="infrastructure">Infrastructure</h1>
<p>The CPU has four 16-bit registers, <code>A</code> (accumulator), <code>B</code> (open register), <code>C</code> (count register), and <code>D</code> (data register). There are also three 16-bit pointers, the <code>RP</code> (read pointer), <code>WP</code> (write pointer), and <code>SP</code> (stack pointer). There is currently only a single interrupt, the <code>UOI</code>, or User Input Overflow Interrupt, which defaults to going back to the first line of code for now, may change later. The CPU also has three flags: <code>OF</code>, <code>OF2</code>, and <code>OF3</code>, all open flags that can be set with the <code>TOGGLE</code> opcode. As for memory, the CPU has 5kb of memory, and a 1kb stack. Memory addresses can be read through the <code>RP</code>&#39;s respective opcode, or by using <code>%[register]</code>, which causes the register to act like a pointer, and call its numeric value from the memory array.</p>
<h1 id="how-to-run-it">How to run it</h1>
<p>Make sure to install the Julia binary from julialang.org or compile it yourself from source. In the console, <code>cd</code> to the folder where <code>cpu.jl</code> is and type <code>julia cpu.jl path/to/file.jlasm</code> then optionally add in <code>true</code> at the end to enter debug mode.</p>
<h1 id="opcodes">OpCodes</h1>
<p><code>write</code> (for writing numerical values, but can also write one-word strings) - <code>WRITE [numeric value, bit size (8 or 16 for all bit sizes hereon)]</code></p>
<p><code>strwrite</code> (for writing strings) - <code>STRWRITE [string]</code></p>
<p><code>read</code> (for reading value to a register) - <code>READ [register, bit size]</code></p>
<p><code>load</code> (for loading values to a register/pointer/memory location) - <code>LOAD [register/pointer/%register, value/register/%register]</code></p>
<p><code>push</code> (for pushing to the stack) - <code>PUSH [value, bit size]</code></p>
<p><code>pop</code> (for popping values from the stack) - <code>POP [location, bit size]</code></p>
<p><code>getin</code> (gets user input up to 255 characters. Is loaded to the last 255 memory locations) - <code>GETIN</code></p>
<p><code>wipein</code> (wipes input) - <code>WIPEIN</code></p>
<p><code>add</code> (adds to values) - <code>ADD [location, value]</code></p>
<p><code>sub</code> (subtracts value 2 from value 1) - <code>SUB [location, value]</code></p>
<p><code>iter</code> (iterates a pointer 1 byte) - <code>ITER [pointer (either RP or WP)]</code></p>
<p><code>jmp</code> (unconditionally jumps to a line #. Use <code>GOTO</code> for labels) - <code>JMP [line]</code></p>
<p><code>jeq</code> (jumps if two values are equal) - <code>JEQ [location, value, line]</code></p>
<p><code>jneq</code> (jumps if two values are not equal) - <code>JNEQ [location, value, line]</code></p>
<p><code>jgt</code> (jumps if value 1 is greater than value 2) - <code>JGT [location, value, line]</code></p>
<p><code>jngt</code> (jumps if value 1 is not greater than value 2) - <code>JNGT [location, value, line]</code></p>
<p><code>jlt</code> (jumps if value 1 is less than value 2) - <code>JLT [location, value, line]</code></p>
<p><code>jnlt</code> (jumps if value 1 is not less than value 2) - <code>JNLT [location, value, line]</code></p>
<p><code>jif</code> (jumps if given open flag is true) - <code>JIF [flag, line]</code></p>
<p><code>toggle</code> (toggles the value of an open flag) - <code>TOGGLE [flag]</code></p>
<p><code>print</code> (prints set amount of bytes from memory at <code>RP</code> to screen. Note that <code>\n</code> properly works) - <code>PRINT [char amount/byte amount]</code></p>
<p><code>goto</code> (goes to label) - <code>GOTO [label]</code></p>
<p><code>call</code> (calls another file) - <code>CALL [filename, amount to pop, label/line to jump to]</code></p>
<p><code>hlt</code> (stops the program) - <code>HLT</code></p>
<h1 id="example">Example</h1>
<h3 id="code-asm-test-stringchecktest-jlasm-">Code (asm-test/stringchecktest.jlasm)</h3>
<pre><code>.start
    push <span class="hljs-number">20</span> <span class="hljs-number">8</span>                 ; Where <span class="hljs-keyword">to</span> jump <span class="hljs-keyword">to</span> <span class="hljs-keyword">after</span> <span class="hljs-keyword">the</span> <span class="hljs-built_in">string</span> test

    strwrite This <span class="hljs-keyword">is</span> equivalent.\n
    <span class="hljs-built_in">write</span> <span class="hljs-number">0</span> <span class="hljs-number">16</span>                ; For <span class="hljs-keyword">some</span> odd reason, double null buffer <span class="hljs-keyword">is</span> needed <span class="hljs-keyword">or</span> rogue <span class="hljs-number">0x54</span> enters <span class="hljs-keyword">the</span> fray
    print <span class="hljs-number">20</span>
    iter WP
    iter RP
    iter RP

    push <span class="hljs-number">1</span> <span class="hljs-number">8</span>                 ; Pointer <span class="hljs-keyword">to</span> <span class="hljs-keyword">first</span> <span class="hljs-built_in">string</span>. <span class="hljs-number">1</span>-based indexing
    push RP <span class="hljs-number">8</span>                ; Pointer <span class="hljs-keyword">to</span> <span class="hljs-keyword">second</span> <span class="hljs-built_in">string</span>

    strwrite This <span class="hljs-keyword">is</span> equivalent.\n
    <span class="hljs-built_in">write</span> <span class="hljs-number">0</span> <span class="hljs-number">8</span>
    print <span class="hljs-number">20</span>

    goto .stringtest

    jif OF <span class="hljs-number">25</span>                ; Check <span class="hljs-keyword">to</span> see <span class="hljs-keyword">if</span> <span class="hljs-keyword">first</span> OF <span class="hljs-keyword">is</span> <span class="hljs-keyword">set</span> <span class="hljs-keyword">to</span> <span class="hljs-literal">true</span>
    strwrite The strings are <span class="hljs-keyword">not</span> <span class="hljs-keyword">equal</span>.
    print <span class="hljs-number">26</span>
    hlt

    strwrite The strings are <span class="hljs-keyword">equal</span>.
    print <span class="hljs-number">22</span>
    hlt

    .stringtest
        pop B <span class="hljs-number">8</span>
        pop D <span class="hljs-number">8</span>

        .stringtestloop
            jeq %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>           ; Check <span class="hljs-keyword">to</span> see <span class="hljs-keyword">if</span> <span class="hljs-keyword">first</span> <span class="hljs-built_in">string</span> has terminated
            jeq %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>           ; Check <span class="hljs-keyword">to</span> see <span class="hljs-keyword">if</span> <span class="hljs-keyword">second</span> <span class="hljs-built_in">string</span> has terminated (<span class="hljs-keyword">if</span> <span class="hljs-keyword">first</span> <span class="hljs-built_in">string</span> hasn't terminated)

            jeq %B %D <span class="hljs-number">41</span>          ; Check <span class="hljs-keyword">to</span> see <span class="hljs-keyword">if</span> <span class="hljs-keyword">the</span> two <span class="hljs-built_in">characters</span> are <span class="hljs-keyword">equal</span>
            pop B <span class="hljs-number">8</span>
            jmp B

            add B <span class="hljs-number">1</span>               ; End <span class="hljs-keyword">of</span> main loop
            add D <span class="hljs-number">1</span>
            goto .stringtestloop

            jeq %D <span class="hljs-number">0</span> <span class="hljs-number">49</span>           ; Check <span class="hljs-keyword">to</span> see <span class="hljs-keyword">if</span> <span class="hljs-keyword">second</span> <span class="hljs-built_in">string</span> has terminated (<span class="hljs-keyword">if</span> <span class="hljs-keyword">the</span> <span class="hljs-keyword">first</span> <span class="hljs-built_in">string</span> HAS terminated)
            pop B <span class="hljs-number">8</span>
            jmp B

            toggle OF             ; If both are terminated <span class="hljs-keyword">without</span> previously jumping out, <span class="hljs-keyword">set</span> OF <span class="hljs-keyword">to</span> <span class="hljs-literal">true</span>
            pop B <span class="hljs-number">8</span>
            jmp B
</code></pre><h3 id="debug-output">Debug Output</h3>
<pre><code>Loading File: asm-test/stringchecktest.jlasm
<span class="hljs-attr">Labels:</span> Dict(<span class="hljs-string">"STRINGTESTLOOP"</span>=&gt;<span class="hljs-number">33</span>,<span class="hljs-string">"STRINGTEST"</span>=&gt;<span class="hljs-number">29</span>,<span class="hljs-string">"START"</span>=&gt;<span class="hljs-number">1</span>)
<span class="hljs-number">1</span>: .START
<span class="hljs-number">2</span>: PUSH <span class="hljs-number">20</span> <span class="hljs-number">8</span>
<span class="hljs-number">3</span>:
<span class="hljs-number">4</span>: STRWRITE THIS IS EQUIVALENT.\N
        Wrote <span class="hljs-string">"This is equivalent.
        "</span> to <span class="hljs-number">1</span> through <span class="hljs-number">21</span>
<span class="hljs-number">5</span>: WRITE <span class="hljs-number">0</span> <span class="hljs-number">16</span>
        Writing <span class="hljs-number">0</span> to <span class="hljs-number">21</span>
<span class="hljs-number">6</span>: PRINT <span class="hljs-number">20</span>
This is equivalent.

<span class="hljs-number">7</span>: ITER WP
<span class="hljs-number">8</span>: ITER RP
<span class="hljs-number">9</span>: ITER RP
<span class="hljs-number">10</span>:
<span class="hljs-number">11</span>: PUSH <span class="hljs-number">1</span> <span class="hljs-number">8</span>
<span class="hljs-number">12</span>: PUSH RP <span class="hljs-number">8</span>
<span class="hljs-number">13</span>:
<span class="hljs-number">14</span>: STRWRITE THIS IS EQUIVALENT.\N
        Wrote <span class="hljs-string">"This is equivalent.
        "</span> to <span class="hljs-number">23</span> through <span class="hljs-number">43</span>
<span class="hljs-number">15</span>: WRITE <span class="hljs-number">0</span> <span class="hljs-number">8</span>
        Writing <span class="hljs-number">0</span> to <span class="hljs-number">43</span>
<span class="hljs-number">16</span>: PRINT <span class="hljs-number">20</span>
This is equivalent.

<span class="hljs-number">17</span>:
<span class="hljs-number">18</span>: GOTO .STRINGTEST
<span class="hljs-number">30</span>: POP B <span class="hljs-number">8</span>
<span class="hljs-number">31</span>: POP D <span class="hljs-number">8</span>
<span class="hljs-number">32</span>:
<span class="hljs-number">33</span>: .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">84</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">84</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">84</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">84</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">104</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">104</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">104</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">104</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">105</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">115</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">32</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">105</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">115</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">115</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">32</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">32</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">101</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">113</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">113</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">113</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">113</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">117</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">117</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">117</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">117</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">105</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">105</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">118</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">118</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">118</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">118</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">97</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">97</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">97</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">97</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">108</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">108</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">108</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">108</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">101</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">101</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">110</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">110</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">110</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">110</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">116</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">116</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">116</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">116</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">46</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">46</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">46</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">46</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">10</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">35</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">38</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">10</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">false</span>
<span class="hljs-number">36</span>:
<span class="hljs-number">37</span>: JEQ %B %D <span class="hljs-number">41</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">10</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">10</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">41</span>: ADD B <span class="hljs-number">1</span>
<span class="hljs-number">42</span>: ADD D <span class="hljs-number">1</span>
<span class="hljs-number">43</span>: GOTO .STRINGTESTLOOP
<span class="hljs-number">34</span>: JEQ %B <span class="hljs-number">0</span> <span class="hljs-number">45</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">0</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">45</span>: JEQ %D <span class="hljs-number">0</span> <span class="hljs-number">49</span>
<span class="hljs-attr">        ARG1:</span> <span class="hljs-number">0</span>
<span class="hljs-attr">        ARG2:</span> <span class="hljs-number">0</span>
        ARG1 == ARG2: <span class="hljs-literal">true</span>
<span class="hljs-number">49</span>: TOGGLE OF
<span class="hljs-attr">        OF:</span> <span class="hljs-literal">true</span>
<span class="hljs-number">50</span>: POP B <span class="hljs-number">8</span>
<span class="hljs-number">51</span>: JMP B
<span class="hljs-number">20</span>: JIF OF <span class="hljs-number">25</span>
<span class="hljs-attr">OF:</span> <span class="hljs-literal">true</span>
<span class="hljs-number">25</span>: STRWRITE THE STRINGS ARE EQUAL.
        Wrote <span class="hljs-string">"The strings are equal."</span> to <span class="hljs-number">43</span> through <span class="hljs-number">65</span>
<span class="hljs-number">26</span>: PRINT <span class="hljs-number">22</span>
The strings are equal.
<span class="hljs-number">27</span>: HLT
</code></pre>
</div>
<div id="comments">
	<ul class="list-group">
		@foreach ($comments as $comment)
			<li class="list-group-item">
				<h5>
					{{ $comment->name }}
				</h5>
				
				<p>
					{{ $comment->content }}
				</p>
			</li>
		@endforeach
	</ul>
</div>
<form action="/comment/julia-vm" method="POST">
	{{ csrf_field() }}
	<p>Name: <input name="name" type="text" /></p>
	<p>Comment: <input name="content" type="text" /></p>
	<p><input type="submit" /></p>
</form>
@endsection